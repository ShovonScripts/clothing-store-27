<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CartLineData;
use App\Models\Coupon;
use App\Models\ProductVariant;

class PricingService
{
    public function __construct(
        protected CartService $cartService,
        protected DeliveryChargeService $deliveryChargeService
    ) {}

    public function calculate(
        int $districtId,
        int $areaId,
        ?string $couponCode = null,
        int $paymentMethod = 1,
        ?iterable $cartLines = null
    ): array {
        $lines = $cartLines ?? $this->cartService->get()->map(fn ($item) => new CartLineData(
            variantId: (int) $item['variant_id'],
            qty: (int) $item['qty'],
        ));

        $lines = collect($lines)->map(function (CartLineData $line) {
            $variant = ProductVariant::with('product')->find($line->variantId);
            if (! $variant) {
                throw new \InvalidArgumentException("Variant {$line->variantId} not found");
            }

            return new CartLineData(
                variantId: $line->variantId,
                qty: $line->qty,
            );
        })->mapWithKeys(function (CartLineData $line) use ($lines) {
            // Find the original line to get price info
            $originalLine = $lines->first(fn ($l) => $l->variantId === $line->variantId);
            $variant = ProductVariant::with('product')->find($line->variantId);

            if (! $variant) {
                throw new \InvalidArgumentException("Variant {$line->variantId} not found");
            }

            return [$line->variantId => [
                'variant_id' => $line->variantId,
                'qty' => $line->qty,
                'price' => $variant->price ?? $variant->product->price,
                'product_name' => $variant->product->name,
                'product_slug' => $variant->product->slug,
                'variant_size' => $variant->size,
                'variant_color' => $variant->color?->name ?? '',
                'variant_color_hex' => $variant->color?->hex ?? '#000000',
                'image' => $variant->product->getFirstImageUrl('thumb'),
            ]];
        });

        $subtotal = $lines->sum(fn ($item) => $item['price'] * $item['qty']);

        $delivery = $this->deliveryChargeService->calculate(
            $districtId,
            $areaId,
            $subtotal
        );

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

            if ($coupon && $coupon->isValidFor($subtotal, 1)) {
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
                'id' => $coupon->id,
            ] : null,
            'total' => max(0, $subtotal + $delivery - $discount),
            'breakdown' => [
                'items' => $lines->map(function ($item) {
                    $variant = ProductVariant::with('product')->find($item['variant_id']);

                    return [
                        'variant_id' => $item['variant_id'],
                        'name' => $item['product_name'],
                        'size' => $item['variant_size'],
                        'color' => $item['variant_color'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'line_total' => $item['price'] * $item['qty'],
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

        if (! $coupon->isValidFor($subtotal, 1)) {
            return ['valid' => false, 'message' => 'Coupon not applicable to this order'];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($subtotal),
        ];
    }
}
