<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    public array $cartItems = [];

    public int $cartCount = 0;

    public int $subtotal = 0;

    public function mount(): void
    {
        $this->refreshCart();
    }

    public function refreshCart(): void
    {
        $cart = app(CartService::class)->getCart();
        $this->cartItems = $cart->toArray();
        $this->cartCount = $cart->sum();
        $this->subtotal = 0; // Will be computed with actual prices
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

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
