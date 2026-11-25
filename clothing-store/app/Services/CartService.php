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
        $variantIds = array_keys($sessionCart);

        if (empty($variantIds)) {
            return collect();
        }

        $variants = ProductVariant::with(['product', 'color'])
            ->whereIn('id', $variantIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($sessionCart)->map(function (int $qty, string $variantId) use ($variants) {
            $variant = $variants->get((int) $variantId);
            if (! $variant) {
                return null;
            }

            return $this->revalidateItemFromLoaded($variant, (int) $qty);
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

    public function add(string|int $variantId, int $qty = 1): array
    {
        $variantId = (string) $variantId;
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

    public function update(string|int $variantId, int $qty): ?array
    {
        $variantId = (string) $variantId;
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

    public function remove(string|int $variantId): void
    {
        $variantId = (string) $variantId;
        $cart = $this->getRaw();
        unset($cart[$variantId]);
        session()->put(self::SESSION_KEY, $cart);
    }

    public function has(string|int $variantId): bool
    {
        $variantId = (string) $variantId;
        $cart = $this->getRaw();

        return isset($cart[$variantId]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    private function revalidateItem(string|int $variantId, int $requestedQty): ?array
    {
        $variant = ProductVariant::with('product')->find((int) $variantId);

        if (! $variant) {
            return null;
        }

        return $this->revalidateItemFromLoaded($variant, $requestedQty);
    }

    private function revalidateItemFromLoaded(ProductVariant $variant, int $requestedQty): ?array
    {
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
            'variant_id' => $variant->id,
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
