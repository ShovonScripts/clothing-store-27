<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public const CART_SESSION_KEY = 'cart';

    public function getCart(): Collection
    {
        return collect(Session::get(self::CART_SESSION_KEY, []));
    }

    public function add(int $variantId, int $quantity = 1): void
    {
        $cart = $this->getCart()->toArray();

        if (isset($cart[$variantId])) {
            $cart[$variantId] += $quantity;
        } else {
            $cart[$variantId] = $quantity;
        }

        Session::put(self::CART_SESSION_KEY, $cart);
    }

    public function update(int $variantId, int $quantity): void
    {
        $cart = $this->getCart()->toArray();

        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = $quantity;
        }

        Session::put(self::CART_SESSION_KEY, $cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->getCart()->toArray();
        unset($cart[$variantId]);
        Session::put(self::CART_SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    public function count(): int
    {
        return $this->getCart()->sum();
    }

    public function isEmpty(): bool
    {
        return $this->getCart()->isEmpty();
    }

    public function getVariantIds(): array
    {
        return array_keys($this->getCart()->toArray());
    }

    public function getQuantity(int $variantId): int
    {
        return $this->getCart()->get($variantId, 0);
    }

    public function getSubtotal(): int
    {
        // This will be computed properly with prices from DB in the actual implementation
        return 0;
    }
}
