<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CartService
{
    public const SESSION_KEY = 'cart';

    public function get(): Collection
    {
        $sessionCart = session()->get(self::SESSION_KEY, []);

        return collect($sessionCart)->map(function (int $qty, string $variantId) {
            return $this->revalidateItem($variantId, $qty);
        })->filter()->values();
    }

    public function getRaw(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return $this->get()->sum('qty');
    }

    public function subtotal(): int
    {
        return $this->get()->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    public function add(int $variantId, int $qty = 1): array
    {
        $cart = $this->getRaw();

        $currentQty = $cart[$variantId] ?? 0;
        $newQty = $currentQty + $qty;

        $validated = $this->revalidateItem($variantId, $newQty);
        if (! $validated) {
            throw new \InvalidArgumentException('Variant unavailable or insufficient stock');
        }

        $cart[$variantId] = $validated['qty'];
        session()->put(self::SESSION_KEY, $cart);

        return $validated;
    }

    public function update(int $variantId, int $qty): ?array
    {
        $cart = $this->getRaw();

        if ($qty <= 0) {
            unset($cart[$variantId]);
            session()->put(self::SESSION_KEY, $cart);

            return null;
        }

        $validated = $this->revalidateItem($variantId, $qty);
        if (! $validated) {
            throw new \InvalidArgumentException('Variant unavailable or insufficient stock');
        }

        $cart[$variantId] = $validated['qty'];
        session()->put(self::SESSION_KEY, $cart);

        return $validated;
    }

    public function remove(int $variantId): void
    {
        $cart = $this->getRaw();
        unset($cart[$variantId]);
        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function has(int $variantId): bool
    {
        $cart = $this->getRaw();

        return isset($cart[$variantId]);
    }

    private function revalidateItem(int $variantId, int $requestedQty): ?array
    {
        $variant = ProductVariant::with('product')->find($variantId);

        if (! $variant) {
            return null;
        }

        if (! $variant->is_active || ! $variant->product->is_active) {
            return null;
        }

        $availableStock = $variant->stock;
        if ($availableStock < $requestedQty) {
            $requestedQty = $availableStock;
        }

        if ($requestedQty <= 0) {
            return null;
        }

        $price = $variant->price ?? $variant->product->price;

        return [
            'variant_id' => $variantId,
            'product_id' => $variant->product_id,
            'product_name' => $variant->product->getDisplayNameAttribute(),
            'product_slug' => $variant->product->slug,
            'variant_size' => $variant->size,
            'variant_color' => $variant->color?->name ?? '',
            'variant_color_hex' => $variant->color?->hex ?? '#000000',
            'image' => $variant->product->getFirstImageUrl('thumb'),
            'price' => $price,
            'qty' => $requestedQty,
            'max_stock' => $availableStock,
        ];
    }
}
