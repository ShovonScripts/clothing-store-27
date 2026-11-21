<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\ProductVariant;
use App\Services\CartService;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    public array $cartItems = [];

    public int $cartCount = 0;

    public int $subtotal = 0;

    public int $shipping = 8000; // 80 BDT

    public function mount(): void
    {
        $this->refreshCart();
    }

    public function refreshCart(): void
    {
        $cart = app(CartService::class)->getCart();
        $variantIds = array_keys($cart->toArray());

        $variants = ProductVariant::whereIn('id', $variantIds)
            ->with(['product.media', 'color'])
            ->get()
            ->keyBy('id');

        $this->cartItems = $cart->map(function ($quantity, $variantId) use ($variants) {
            $variant = $variants->get($variantId);
            if (! $variant) {
                return null;
            }

            return [
                'variant' => $variant,
                'quantity' => $quantity,
                'subtotal' => $variant->getEffectivePrice() * $quantity,
            ];
        })->filter()->toArray();

        $this->cartCount = array_sum(array_column($this->cartItems, 'quantity'));
        $this->subtotal = array_sum(array_column($this->cartItems, 'subtotal'));
    }

    public function open(): void
    {
        $this->refreshCart();
        $this->isOpen = true;
        $this->dispatch('cart-drawer-opened');
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function updateQuantity(int $variantId, int $quantity): void
    {
        app(CartService::class)->update($variantId, $quantity);
        $this->refreshCart();
        $this->dispatch('cart-updated');
    }

    public function remove(int $variantId): void
    {
        app(CartService::class)->remove($variantId);
        $this->refreshCart();
        $this->dispatch('cart-updated');
    }

    public function clear(): void
    {
        app(CartService::class)->clear();
        $this->refreshCart();
        $this->dispatch('cart-updated');
    }

    public function getTotalProperty(): int
    {
        return $this->subtotal + ($this->subtotal >= 200000 ? 0 : $this->shipping);
    }

    public function getFreeShippingThreshold(): int
    {
        return 200000; // 2000 BDT
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
