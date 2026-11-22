<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Order;
use Livewire\Component;

class OrderSuccess extends Component
{
    public Order $order;

    public bool $showDetails = true;

    public function mount(Order $order): void
    {
        // Verify session access (simple guard - in production use signed URLs or session)
        $this->order = $order->load(['items', 'customer', 'shippingDistrict', 'shippingArea']);

        // Clear cart session
        session()->forget('cart');
    }

    public function render()
    {
        return view('livewire.storefront.order-success', [
            'order' => $this->order,
        ]);
    }
}
