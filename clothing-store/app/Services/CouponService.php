<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function validate(string $code, int $subtotal, int $paymentMethod): array
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

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached'];
        }

        if ($coupon->per_user_limit) {
            // TODO: Check per-user usage
        }

        if ($subtotal < $coupon->min_order_amount) {
            return ['valid' => false, 'message' => 'Minimum order amount not met'];
        }

        if ($coupon->allowed_payment_methods && ! in_array($paymentMethod, $coupon->allowed_payment_methods)) {
            return ['valid' => false, 'message' => 'Coupon not valid for selected payment method'];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($subtotal),
        ];
    }

    public function apply(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
