<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\PricingService;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class PlaceOrderAction
{
    public function __construct(
        protected CartService $cartService,
        protected PricingService $pricingService,
        protected StockService $stockService
    ) {}

    public function execute(array $data): Order
    {
        // Validate idempotency token
        $token = $data['idempotency_token'] ?? null;
        if (! $token) {
            throw new \InvalidArgumentException('Idempotency token required');
        }

        // Check for duplicate order with same token
        $existingOrder = Order::where('order_number', 'like', "%{$token}%")->first();
        if ($existingOrder) {
            return $existingOrder;
        }

        // Get cart items - use provided cart_items for testing, otherwise read from session
        $cartItems = isset($data['cart_items']) ? collect($data['cart_items']) : $this->cartService->get();
        if ($cartItems->isEmpty()) {
            throw new \InvalidArgumentException('Cart is empty');
        }

        // Re-validate stock and prices
        $items = $cartItems->map(function ($item) {
            return [
                'variant_id' => $item['variant_id'],
                'qty' => $item['qty'],
            ];
        })->toArray();

        $this->stockService->reserve($items);

        return DB::transaction(function () use ($data, $token, $cartItems) {
            // Create or get customer
            $customer = Customer::firstOrCreate(
                ['phone' => $data['phone']],
                ['name' => $data['name'] ?? null, 'email' => $data['email'] ?? null]
            );

            if ($customer->is_blacklisted) {
                throw new \InvalidArgumentException('Customer is blacklisted');
            }

            // Check per-phone daily limit
            $maxPending = config('settings.max_pending_orders_per_phone_per_day', 3);
            $pendingToday = Order::where('customer_id', $customer->id)
                ->whereDate('created_at', now())
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            if ($pendingToday >= $maxPending) {
                throw new \InvalidArgumentException("Daily order limit reached (max {$maxPending})");
            }

            // Calculate pricing
            $pricing = $this->pricingService->calculate(
                $data['district_id'],
                $data['area_id'],
                $data['coupon_code'] ?? null,
                $data['payment_method'] === 'bkash' ? 2 : 1,
                $cartItems
            );

            // Verify totals match (price tamper check)
            $clientTotal = $data['total'] ?? 0;
            if ($clientTotal !== $pricing['total']) {
                throw new \InvalidArgumentException('Price mismatch. Server total: '.$pricing['total']);
            }

            // Generate order number: YYMMDD + 4-digit sequence
            $datePrefix = now()->format('ymd');
            $sequence = Order::where('order_number', 'like', $datePrefix.'%')->count() + 1;
            $orderNumber = $datePrefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber.$token, // embed token for idempotency
                'customer_id' => $customer->id,
                'subtotal' => $pricing['subtotal'],
                'delivery_charge' => $pricing['delivery_charge'],
                'discount' => $pricing['discount'],
                'total' => $pricing['total'],
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_method'] === 'bkash' ? 'awaiting_verification' : 'unpaid',
                'shipping_name' => $data['name'],
                'shipping_phone' => $data['phone'],
                'shipping_district_id' => $data['district_id'],
                'shipping_area_id' => $data['area_id'],
                'shipping_address' => $data['address'],
                'note' => $data['note'] ?? null,
                'coupon_id' => $pricing['coupon']['id'] ?? null,
                'discount_amount' => $pricing['discount'],
                'items_snapshot' => $pricing['breakdown']['items'],
            ]);

            // Create order items
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
                'method' => $data['payment_method'],
                'status' => $data['payment_method'] === 'bkash' ? 'awaiting_verification' : 'unpaid',
                'bkash_trxid' => $data['bkash_trxid'] ?? null,
                'bkash_sender' => $data['bkash_sender'] ?? null,
                'amount' => $pricing['total'],
            ]);

            // Handle bKash data
            if ($data['payment_method'] === 'bkash') {
                $order->update([
                    'bkash_trxid' => $data['bkash_trxid'],
                    'bkash_sender' => $data['bkash_sender'],
                    'bkash_amount' => $data['bkash_amount'],
                ]);
            }

            // Create status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'pending',
                'from_payment_status' => null,
                'to_payment_status' => $data['payment_method'] === 'bkash' ? 'awaiting_verification' : 'unpaid',
                'note' => 'Order placed via checkout',
            ]);

            // Clear cart
            $this->cartService->clear();

            // Update customer pending orders count
            $customer->increment('pending_orders_today');
            $customer->update(['last_order_date' => now()]);

            return $order;
        });
    }
}
