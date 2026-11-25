<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\OrderPlaced;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\ActivityLogger;
use App\Services\BlacklistService;
use App\Services\CartService;
use App\Services\DeliveryChargeService;
use App\Services\OrderNumberService;
use App\Services\PricingService;
use App\Services\SettingsService;
use App\Services\StockService;
use App\Support\PhoneNormalizer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PlaceOrderAction
{
    public function __construct(
        protected CartService $cartService,
        protected PricingService $pricingService,
        protected StockService $stockService,
        protected BlacklistService $blacklistService,
        protected OrderNumberService $orderNumberService,
        protected ActivityLogger $activityLogger,
    ) {}

    public function execute(CheckoutData $data): Order
    {
        if (! $data->idempotencyToken) {
            throw new \InvalidArgumentException('Idempotency token required');
        }

        $cacheKey = "checkout_idempotency:{$data->idempotencyToken}";
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        if ($data->lines->isEmpty()) {
            throw new \InvalidArgumentException('Cart is empty');
        }

        $phone = PhoneNormalizer::normalize($data->phone) ?: $data->phone;
        $isBkash = in_array($data->paymentMethod, ['bkash', PaymentMethod::BkashSendMoney->value], true);
        $method = $isBkash ? PaymentMethod::BkashSendMoney : PaymentMethod::Cod;

        if ($this->blacklistService->isBlocked($phone, $data->ipAddress, $data->address)) {
            throw new \InvalidArgumentException('Order cannot be processed');
        }

        if (app(SettingsService::class)->get('maintenance_orders_paused', '0') === '1') {
            throw new \InvalidArgumentException('Orders are currently paused');
        }

        $trxid = null;
        if ($isBkash) {
            if (! $data->bkashTrxid || ! $data->bkashSender || $data->bkashAmount <= 0) {
                throw new \InvalidArgumentException('bKash details required');
            }

            $trxid = strtoupper(preg_replace('/\s+/', '', $data->bkashTrxid) ?? '');
            if (preg_match('/^[A-Z0-9]{8,12}$/', $trxid) !== 1) {
                throw new \InvalidArgumentException('Invalid bKash TrxID');
            }

            if (Payment::where('bkash_trxid', $trxid)->exists() || Order::where('bkash_trxid', $trxid)->exists()) {
                throw new \InvalidArgumentException('Duplicate transaction ID');
            }
        }

        $items = $data->lines->map(fn ($line) => [
            'variant_id' => $line->variantId,
            'qty' => $line->qty,
        ])->all();

        return DB::transaction(function () use ($data, $phone, $method, $isBkash, $trxid, $items, $cacheKey) {
            $this->stockService->reserve($items);

            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                ['name' => $data->name ?? null, 'email' => $data->email ?? null]
            );

            if ($customer->is_blacklisted) {
                throw new \InvalidArgumentException('Order cannot be processed');
            }

            // Check blacklists table (phone, IP, address keyword)
            $ip = $data->ipAddress ?? request()->ip();
            if ($this->blacklistService->isBlocked($phone, $ip, $data->address)) {
                throw new \InvalidArgumentException('Order cannot be processed');
            }

            $maxPending = (int) app(SettingsService::class)->get('max_pending_orders_per_phone_per_day', 3);
            $pendingToday = Order::whereHas('customer', fn ($q) => $q->where('phone', $phone))
                ->whereDate('created_at', now())
                ->whereIn('status', [OrderStatus::Pending, OrderStatus::Confirmed])
                ->count();
            if ($pendingToday >= $maxPending) {
                throw new \InvalidArgumentException("Daily order limit reached (max {$maxPending})");
            }

            $pricing = $this->pricingService->calculate(
                $data->districtId,
                $data->areaId,
                $data->couponCode ?: null,
                $isBkash ? 2 : 1,
                $data->lines,
                $phone,
            );

            if ($data->total !== $pricing['total']) {
                throw new \InvalidArgumentException('Price mismatch');
            }

            $minOrder = (int) app(SettingsService::class)->get('min_order_amount', 0);
            if ($minOrder > 0 && $pricing['total'] < $minOrder) {
                throw new \InvalidArgumentException('Order total is below the minimum order amount');
            }

            if ($isBkash && $data->bkashAmount < $pricing['total']) {
                throw new \InvalidArgumentException('bKash amount must be at least the order total');
            }

            if ($data->couponCode) {
                $coupon = Coupon::where('code', strtoupper($data->couponCode))->first();
                if (! $coupon || ! $coupon->isValidFor($pricing['subtotal'], $method, $phone)) {
                    throw new \InvalidArgumentException('Invalid or expired coupon code');
                }
            }

            $orderNumber = $this->orderNumberService->next();
            $zone = app(DeliveryChargeService::class)->getZone($data->districtId, $data->areaId);

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'subtotal' => $pricing['subtotal'],
                'delivery_charge' => $pricing['delivery_charge'],
                'discount' => $pricing['discount'],
                'total' => $pricing['total'],
                'status' => OrderStatus::Pending,
                'payment_method' => $method,
                'payment_status' => $isBkash ? PaymentStatus::AwaitingVerification : PaymentStatus::Unpaid,
                'bkash_trxid' => $trxid,
                'bkash_sender' => $isBkash ? $data->bkashSender : null,
                'bkash_amount' => $isBkash ? $data->bkashAmount : null,
                'shipping_name' => $data->name,
                'shipping_phone' => $phone,
                'customer_email' => $data->email,
                'shipping_district_id' => $data->districtId,
                'shipping_area_id' => $data->areaId,
                'shipping_address' => $data->address,
                'delivery_zone_key' => $zone,
                'note' => $data->note ?: null,
                'coupon_id' => $pricing['coupon']['id'] ?? null,
                'discount_amount' => $pricing['discount'],
                'cod_amount' => $method === PaymentMethod::Cod ? $pricing['total'] : 0,
                'items_snapshot' => $pricing['breakdown']['items'],
                'ip_address' => $data->ipAddress,
                'user_agent' => request()->userAgent(),
                'source' => request()->input('utm_source'),
                'utm_source' => session('utm_params.utm_source'),
                'utm_medium' => session('utm_params.utm_medium'),
                'utm_campaign' => session('utm_params.utm_campaign'),
                'utm_term' => session('utm_params.utm_term'),
                'utm_content' => session('utm_params.utm_content'),
                'fbclid' => session('utm_params.fbclid'),
                'gclid' => session('utm_params.gclid'),
                'landing_page' => session('utm_params.landing_page'),
                'referrer' => session('utm_params.referrer'),
            ]);

            foreach ($pricing['breakdown']['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'product_slug' => $item['product_slug'],
                    'variant_size' => $item['size'],
                    'variant_color' => $item['color'],
                    'image' => $item['image'] ?? '',
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'line_total' => $item['line_total'],
                ]);
            }

            if ($pricing['coupon']) {
                $coupon = Coupon::find($pricing['coupon']['id']);
                $coupon->increment('used_count');
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'order_id' => $order->id,
                    'phone' => $phone,
                ]);
            }

            try {
                Payment::create([
                    'order_id' => $order->id,
                    'method' => $method,
                    'status' => $isBkash ? PaymentStatus::AwaitingVerification : PaymentStatus::Unpaid,
                    'bkash_trxid' => $trxid,
                    'bkash_sender' => $isBkash ? $data->bkashSender : null,
                    'bkash_amount_sent' => $isBkash ? $data->bkashAmount : null,
                    'bkash_receiver_number' => $isBkash ? app(SettingsService::class)->get('bkash_number') : null,
                    'amount' => $pricing['total'],
                ]);
            } catch (QueryException $e) {
                throw new \InvalidArgumentException('Duplicate transaction ID');
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => OrderStatus::Pending,
                'from_payment_status' => null,
                'to_payment_status' => $order->payment_status,
                'type' => 'order',
                'note' => 'Order placed via checkout',
            ]);

            $this->activityLogger->log($order, 'orders', 'Order placed', null, [
                'order_number' => $order->order_number,
                'payment_method' => $order->payment_method->value,
                'total' => $order->total,
            ]);

            $customer->increment('total_orders');
            $customer->update([
                'pending_orders_today' => $customer->pending_orders_today + 1,
                'last_order_date' => now()->toDateString(),
            ]);

            $this->cartService->clear();

            cache()->put($cacheKey, $order, now()->addMinutes(10));

            DB::afterCommit(fn () => event(new OrderPlaced($order)));

            return $order;
        });
    }
}
