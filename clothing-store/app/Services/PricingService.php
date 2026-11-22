<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;

class PricingService
{
    public function __construct(
        protected CartService $cartService,
        protected DeliveryChargeService $deliveryChargeService
    ) {}

    public function calculate(int $districtId, int $areaId, ?string $couponCode = null, int $paymentMethod = 1): array
    {
        $items = $this->cartService->get();
        $subtotal = $items->sum(fn ($item) => $item['price'] * $item['qty']);

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
                'items' => $items->map(fn ($item) => [
                    'variant_id' => $item['variant_id'],
                    'name' => $item['product_name'],
                    'size' => $item['variant_size'],
                    'color' => $item['variant_color'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'line_total' => $item['price'] * $item['qty'],
                ])->values()->toArray(),
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
