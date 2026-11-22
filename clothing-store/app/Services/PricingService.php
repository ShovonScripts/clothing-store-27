<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;
use App\Models\ProductVariant;

class PricingService
{
    public function __construct(
        protected CartService $cartService,
        protected DeliveryChargeService $deliveryChargeService
    ) {}

    public function calculate(int $districtId, int $areaId, ?string $couponCode = null, int $paymentMethod = 1, ?iterable $cartItems = null): array
    {
        $items = $cartItems ?? $this->cartService->get();

        // Ensure items have price - fetch from variant if not present
        $items = collect($items)->map(function ($item) {
            if (! isset($item['price'])) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                if ($variant) {
                    $item['price'] = $variant->price ?? $variant->product->price;
                }
            }

            return $item;
        });

        $subtotal = $items->sum(fn ($item) => ($item['price'] ?? 0) * ($item['qty'] ?? 0));

        $delivery = $this->deliveryChargeService->calculate($districtId, $areaId, $subtotal);
        $discount = 0;
        $coupon = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper($couponCode))
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->first();

            if ($coupon && $coupon->isValidFor($subtotal, $paymentMethod)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                $coupon = null;
            }
        }

        $total = $subtotal + $delivery - $discount;

        return [
            'subtotal' => $subtotal,
            'delivery_charge' => $delivery,
            'discount' => $discount,
            'coupon' => $coupon ? [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount' => $discount,
            ] : null,
            'total' => max(0, $total),
            'breakdown' => [
                'items' => $items->map(function ($item) {
                    $variant = ProductVariant::with('product')->find($item['variant_id']);
                    $productName = $variant?->product?->name ?? ($item['product_name'] ?? 'Unknown Product');
                    $size = $variant?->size ?? ($item['variant_size'] ?? '');
                    $color = $variant?->color?->name ?? ($item['variant_color'] ?? '');
                    $price = $item['price'] ?? ($variant?->price ?? $variant?->product?->price ?? 0);
                    $qty = $item['qty'] ?? 0;

                    return [
                        'variant_id' => $item['variant_id'],
                        'name' => $productName,
                        'size' => $size,
                        'color' => $color,
                        'price' => $price,
                        'qty' => $qty,
                        'line_total' => $price * $qty,
                    ];
                })->values()->toArray(),
            ],
        ];
    }

    public function validateCoupon(string $code, int $subtotal, int $paymentMethod): array
    {
        $coupon = Coupon::where('code', strtoupper($code))
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->first();

        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired coupon code'];
        }

        if (! $coupon->isValidFor($subtotal, $paymentMethod)) {
            return ['valid' => false, 'message' => 'Coupon not applicable to this order'];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($subtotal),
        ];
    }
}
