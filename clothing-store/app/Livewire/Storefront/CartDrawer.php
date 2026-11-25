<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class CartDrawer extends Component
{
    public bool $isOpen = false;

    protected $listeners = ['open-cart' => 'open', 'close-cart' => 'close', 'cart-updated' => '$refresh'];

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function updateQty(int $variantId, int $qty): void
    {
        if ($qty <= 0) {
            $this->remove($variantId);

            return;
        }

        try {
            app(CartService::class)->update($variantId, $qty);
            $this->dispatch('cart-updated');
            $this->dispatch('cart-count-changed', count: app(CartService::class)->count());
            $this->dispatch('notify', message: 'Cart updated', type: 'success');
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function remove(int $variantId): void
    {
        app(CartService::class)->remove($variantId);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Item removed', type: 'success');
    }

    public function clear(): void
    {
        app(CartService::class)->clear();
        $this->dispatch('cart-updated');
        $this->isOpen = false;
    }

    public function getCartItemsProperty()
    {
        return app(CartService::class)->get();
    }

    public function getSubtotalProperty(): int
    {
        return app(CartService::class)->subtotal();
    }

    public function getCountProperty(): int
    {
        return app(CartService::class)->count();
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer', [
            'items' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'count' => $this->count,
        ]);
    }
}
