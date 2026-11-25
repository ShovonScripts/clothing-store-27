<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CartLineData;
use App\Enums\PaymentMethod;
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
        ?iterable $cartLines = null,
        ?string $phone = null,
    ): array {
        $method = $paymentMethod === 2
            ? PaymentMethod::BkashSendMoney->value
            : PaymentMethod::Cod->value;

        $lines = $cartLines ?? $this->cartService->get()->map(fn ($item) => new CartLineData(
            variantId: (int) $item['variant_id'],
            qty: (int) $item['qty'],
        ));

        $variantIds = collect($lines)->pluck('variantId')->unique();
        $variants = ProductVariant::with(['product', 'color'])
            ->whereIn('id', $variantIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $lines = collect($lines)->mapWithKeys(function (CartLineData $line) use ($variants) {
            $variant = $variants->get($line->variantId);

            if (! $variant) {
                throw new \InvalidArgumentException("Variant {$line->variantId} not found");
            }

            if (! $variant->is_active || ! $variant->product->is_active) {
                throw new \InvalidArgumentException("Variant {$line->variantId} is unavailable");
            }

            return [$line->variantId => [
                'variant_id' => $line->variantId,
                'qty' => $line->qty,
                'price' => $variant->price ?? $variant->product->price,
                'product_id' => $variant->product_id,
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

        if ($couponCode && strlen(trim($couponCode)) > 0) {
            $coupon = Coupon::where('code', strtoupper($couponCode))
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->first();

            if ($coupon && $coupon->isValidFor($subtotal, $method, $phone)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                $coupon = null;
            }
        }

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
                    return [
                        'variant_id' => $item['variant_id'],
                        'product_id' => $item['product_id'],
                        'name' => $item['product_name'],
                        'product_slug' => $item['product_slug'],
                        'size' => $item['variant_size'],
                        'color' => $item['variant_color'],
                        'image' => $item['image'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'line_total' => $item['price'] * $item['qty'],
                    ];
                })->values()->toArray(),
            ],
        ];
    }

    public function validateCoupon(string $code, int $subtotal, int $paymentMethod, ?string $phone = null): array
    {
        $method = $paymentMethod === 2
            ? PaymentMethod::BkashSendMoney->value
            : PaymentMethod::Cod->value;

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

        if (! $coupon->isValidFor($subtotal, $method, $phone)) {
            return ['valid' => false, 'message' => 'Coupon not applicable to this order'];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($subtotal),
        ];
    }
}
