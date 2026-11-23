<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DailyCounter;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\PricingService;
use App\Services\SettingsService;
use App\Services\StockService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\DB;

class PlaceOrderAction
{
    public function __construct(
        protected CartService $cartService,
        protected PricingService $pricingService,
        protected StockService $stockService
    ) {}

    public function execute(CheckoutData $data): Order
    {
        // Validate idempotency token (separate from order number)
        if (! $data->idempotencyToken) {
            throw new \InvalidArgumentException('Idempotency token required');
        }

        // Check for duplicate order with same idempotency token (stored in session token cache)
        $cacheKey = "checkout_idempotency:{$data->idempotencyToken}";
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        // Validate cart lines
        if ($data->lines->isEmpty()) {
            throw new \InvalidArgumentException('Cart is empty');
        }

        // Re-validate stock and prices for each line
        $items = $data->lines->map(function (CartLineData $line) {
            return [
                'variant_id' => $line->variantId,
                'qty' => $line->qty,
            ];
        })->toArray();

        $this->stockService->reserve($items);

        return DB::transaction(function () use ($data) {
            // Create or get customer FIRST
            $customer = Customer::firstOrCreate(
                ['phone' => $data->phone],
                ['name' => $data->name ?? null, 'email' => $data->email ?? null]
            );

            // Check blacklist (normalize both phones for comparison)
            if ($customer->is_blacklisted && PhoneNormalizer::normalize($customer->phone) === PhoneNormalizer::normalize($data->phone)) {
                throw new \InvalidArgumentException('Order cannot be processed');
            }

            // Check orders-paused setting
            if (app(SettingsService::class)->get('maintenance_orders_paused', '0') === '1') {
                throw new \InvalidArgumentException('Orders are currently paused');
            }

            // Check per-phone daily pending limit
            $maxPending = (int) config('settings.max_pending_orders_per_phone_per_day', 3);
            $pendingToday = Order::whereHas('customer', fn ($q) => $q->where('phone', $data->phone))
                ->whereDate('created_at', now())
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            if ($pendingToday >= $maxPending) {
                throw new \InvalidArgumentException("Daily order limit reached (max {$maxPending})");
            }

            // Calculate pricing with explicit cart lines
            $pricing = $this->pricingService->calculate(
                $data->districtId,
                $data->areaId,
                $data->couponCode,
                $data->paymentMethod === 'bkash' ? 2 : 1,
                $data->lines
            );

            // Verify totals match (price tamper check)
            $clientTotal = $data->total;
            if ($clientTotal !== $pricing['total']) {
                throw new \InvalidArgumentException('Price mismatch');
            }

            // Validate bKash amount if applicable
            if ($data->paymentMethod === 'bkash') {
                if (! $data->bkashTrxid || ! $data->bkashSender || $data->bkashAmount <= 0) {
                    throw new \InvalidArgumentException('bKash details required');
                }
                if ($data->bkashAmount !== $pricing['total']) {
                    throw new \InvalidArgumentException('bKash amount must match order total');
                }
                // Check for duplicate TrxID (case/space insensitive)
                $normalizedTrxid = strtoupper(str_replace(' ', '', $data->bkashTrxid));
                if (Payment::whereRaw("UPPER(REPLACE(bkash_trxid, ' ', '')) = ?", [$normalizedTrxid])->exists()) {
                    throw new \InvalidArgumentException('Duplicate transaction ID');
                }
            }

            // Validate coupon if provided
            if ($data->couponCode) {
                $coupon = Coupon::where('code', strtoupper($data->couponCode))
                    ->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    })
                    ->first();

                if (! $coupon || ! $coupon->isValidFor($pricing['subtotal'], $data->paymentMethod === 'bkash' ? 2 : 1, $data->phone)) {
                    throw new \InvalidArgumentException('Invalid or expired coupon code');
                }
            }

            // Generate order number from daily_counters: YYMMDD + 4-digit sequence
            $datePrefix = now()->format('ymd');
            $date = now()->toDateString();
            // Use raw SQL to atomically get or create the counter and increment
            DB::statement('INSERT OR IGNORE INTO daily_counters (date, sequence, created_at, updated_at) VALUES (?, 0, datetime("now"), datetime("now"))', [$date]);
            DB::update('UPDATE daily_counters SET sequence = sequence + 1, updated_at = datetime("now") WHERE date = ?', [$date]);
            $counter = DailyCounter::where('date', $date)->firstOrFail();
            $sequence = (int) $counter->sequence;
            $orderNumber = $datePrefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'subtotal' => $pricing['subtotal'],
                'delivery_charge' => $pricing['delivery_charge'],
                'discount' => $pricing['discount'],
                'total' => $pricing['total'],
                'status' => OrderStatus::Pending,
                'payment_method' => $data->paymentMethod === 'bkash' ? PaymentMethod::Bkash : PaymentMethod::Cod,
                'payment_status' => $data->paymentMethod === 'bkash' ? PaymentStatus::AwaitingVerification : PaymentStatus::Unpaid,
                'bkash_trxid' => $data->bkashTrxid,
                'bkash_sender' => $data->bkashSender,
                'bkash_amount' => $data->bkashAmount,
                'shipping_name' => $data->name,
                'shipping_phone' => $data->phone,
                'shipping_district_id' => $data->districtId,
                'shipping_area_id' => $data->areaId,
                'shipping_address' => $data->address,
                'note' => $data->note ?? null,
                'coupon_id' => $pricing['coupon']['id'] ?? null,
                'discount_amount' => $pricing['discount'],
                'items_snapshot' => $pricing['breakdown']['items'],
            ]);

            // Create order items from pricing breakdown
            foreach ($pricing['breakdown']['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => ProductVariant::find($item['variant_id'])->product_id,
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'product_slug' => ProductVariant::find($item['variant_id'])->product->slug,
                    'variant_size' => $item['size'],
                    'variant_color' => $item['color'],
                    'image' => ProductVariant::find($item['variant_id'])->product->getFirstImageUrl('thumb'),
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'line_total' => $item['line_total'],
                ]);
            }

            // Apply coupon usage
            if ($pricing['coupon']) {
                $coupon = Coupon::find($pricing['coupon']['id']);
                $coupon->increment('used_count');
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $data->paymentMethod === 'bkash' ? PaymentMethod::Bkash : PaymentMethod::Cod,
                'status' => $data->paymentMethod === 'bkash' ? PaymentStatus::AwaitingVerification : PaymentStatus::Unpaid,
                'bkash_trxid' => $data->bkashTrxid,
                'bkash_sender' => $data->bkashSender,
                'amount' => $pricing['total'],
            ]);

            // Handle bKash data on order
            if ($data->paymentMethod === 'bkash') {
                $order->update([
                    'bkash_trxid' => $data->bkashTrxid,
                    'bkash_sender' => $data->bkashSender,
                    'bkash_amount' => $data->bkashAmount,
                ]);
            }

            // Create status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => OrderStatus::Pending,
                'from_payment_status' => null,
                'to_payment_status' => $data->paymentMethod === 'bkash' ? PaymentStatus::AwaitingVerification : PaymentStatus::Unpaid,
                'note' => 'Order placed via checkout',
            ]);

            // Clear cart
            $this->cartService->clear();

            // Store idempotency token in cache (10 min TTL)
            cache()->put("checkout_idempotency:{$data->idempotencyToken}", $order, now()->addMinutes(10));

            return $order;
        });
    }
}
