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

    public ?string $maskedPhone = null;

    public function track(): void
    {
        $this->validate([
            'orderNumber' => 'required|string|min:10|max:20',
            'phone' => 'required|string|min:11|max:14',
        ]);

        $normalizedPhone = PhoneNormalizer::normalize($this->phone);
        $key = 'track-order:'.$this->orderNumber.':'.$normalizedPhone;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->error = __('Too many attempts. Please wait before trying again.');

            return;
        }

        RateLimiter::hit($key, 300);

        $this->order = Order::where('order_number', $this->orderNumber)
            ->where(function ($query) use ($normalizedPhone) {
                $query->where('shipping_phone', $normalizedPhone)
                    ->orWhereHas('customer', fn ($q) => $q->where('phone', $normalizedPhone));
            })
            ->with(['items', 'statusHistory.changedBy', 'shippingDistrict', 'shippingArea', 'payment'])
            ->first();

        if (! $this->order) {
            $this->error = __('Order not found. Please check your order number and phone number.');

            return;
        }

        $this->maskedPhone = PhoneNormalizer::mask($this->order->shipping_phone);

        $this->history = $this->order->statusHistory
            ->map(fn ($h) => [
                'status' => $h->to_status?->label(),
                'status_value' => $h->to_status?->value,
                'payment_status' => $h->to_payment_status?->label(),
                'note' => $h->note,
                'date' => $h->created_at->format('Y-m-d H:i'),
                'by' => $h->changedBy?->name,
            ])
            ->toArray();

        $this->submitted = true;
    }

    public function getStatusBadgeClass(string|object|null $status): string
    {
        $value = $status instanceof \BackedEnum ? $status->value : (string) $status;

        return match ($value) {
            'pending' => 'bg-amber-100 text-amber-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-indigo-100 text-indigo-800',
            'shipped' => 'bg-violet-100 text-violet-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'returned' => 'bg-orange-100 text-orange-800',
            'failed_delivery' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusBadgeClass(string|object|null $status): string
    {
        $value = $status instanceof \BackedEnum ? $status->value : (string) $status;

        return match ($value) {
            'unpaid' => 'bg-gray-100 text-gray-800',
            'awaiting_verification' => 'bg-amber-100 text-amber-800',
            'paid' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        return view('livewire.storefront.track-order');
    }
}
