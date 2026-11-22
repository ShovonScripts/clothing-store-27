<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
