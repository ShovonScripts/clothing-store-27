<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Order;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $orderNumber = '';

    public string $phone = '';

    public ?Order $order = null;

    public array $history = [];

    public ?string $error = null;

    public bool $submitted = false;

    public function track(): void
    {
        $this->validate([
            'orderNumber' => 'required|string|min:10|max:20',
            'phone' => 'required|string|min:11|max:14',
        ]);

        $key = 'track-order:'.$this->orderNumber.':'.$this->phone;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->error = 'Too many attempts. Please wait before trying again.';

            return;
        }

        RateLimiter::hit($key, 300); // 5 minutes

        $normalizedPhone = PhoneNormalizer::normalize($this->phone);

        $this->order = Order::where('order_number', $this->orderNumber)
            ->whereHas('customer', fn ($q) => $q->where('phone', $this->phone))
            ->with(['items.product', 'items.variant', 'statusHistory.changedBy', 'shippingDistrict', 'shippingArea'])
            ->first();

        if (! $this->order) {
            $this->error = 'Order not found. Please check your order number and phone.';

            return;
        }

        $this->history = $this->order->statusHistory
            ->map(fn ($h) => [
                'status' => $h->to_status,
                'payment_status' => $h->to_payment_status,
                'note' => $h->note,
                'date' => $h->created_at->format('Y-m-d H:i'),
            ])
            ->toArray();

        $this->submitted = true;
    }

    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-indigo-100 text-indigo-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'returned' => 'bg-gray-100 text-gray-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'unpaid' => 'bg-gray-100 text-gray-800',
            'pending_verification' => 'bg-yellow-100 text-yellow-800',
            'verified' => 'bg-green-100 text-green-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        return view('livewire.storefront.track-order');
    }
}
