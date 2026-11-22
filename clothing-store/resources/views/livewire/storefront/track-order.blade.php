@extends('layouts.app')

@section('title', __('Track Order') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    @livewire('storefront.cart-drawer')

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Track Your Order') }}</h1>
                <p class="text-gray-500 mt-2">{{ __('Enter your order number and phone number to track your order') }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-6 mb-6">
                <form wire:submit.prevent="track" class="space-y-4">
                    <div>
                        <label for="orderNumber" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order Number') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="orderNumber" id="orderNumber" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="e.g., 2311220001" required>
                        @error('orderNumber') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                        <input type="tel" wire:model.defer="phone" id="phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="01XXXXXXXXX" required>
                        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($error)
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" role="alert">
                            {{ $error }}
                        </div>
                    @endif

                    <button type="submit" :disabled="submitted" class="w-full py-3 px-6 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ $submitted ? __('Tracking...') : __('Track Order') }}
                    </button>
                </form>
            </div>

            @if ($order)
                <div class="bg-white rounded-xl border border-gray-100 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Order Details') }}</h2>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $getStatusBadgeClass($order->status) }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                        <div>
                            <p class="text-gray-500">{{ __('Order Number') }}</p>
                            <p class="font-mono font-medium text-gray-900">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('Order Date') }}</p>
                            <p class="font-medium text-gray-900">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('Payment Method') }}</p>
                            <p class="font-medium text-gray-900">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'bKash' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('Payment Status') }}</p>
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $getPaymentStatusBadgeClass($order->payment_status) }}">
                                {{ $order->payment_status }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Shipping Address') }}</h3>
                        <address class="text-sm text-gray-600 not-italic">
                            <p>{{ $order->shipping_name }}</p>
                            <p>{{ $order->shipping_phone }}</p>
                            <p>{{ $order->shipping_address }}, {{ $order->shippingArea?->name }}, {{ $order->shippingDistrict?->name }}</p>
                        </address>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Order Items') }}</h3>
                        <div class="space-y-3">
                            @foreach ($order->items as $item)
                                <div class="flex gap-3 p-3 bg-gray-50 rounded-lg">
                                    <img src="{{ $item->image ?? asset('images/placeholder.svg') }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->variant_size }} / {{ $item->variant_color }} × {{ $item->qty }}</p>
                                    </div>
                                    <p class="font-medium text-gray-900">{{ moneyShort($item->line_total) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Order Timeline') }}</h3>
                        <div class="space-y-4">
                            @foreach ($history as $entry)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-2 h-2 bg-primary rounded-full mt-2"></div>
                                    <div class="flex-1 pl-4 border-l-2 border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">{{ $entry['status'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $entry['date'] }}</p>
                                        @if ($entry['note'])
                                            <p class="text-xs text-gray-400 mt-1">{{ $entry['note'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Order Summary') }}</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('Subtotal') }}</dt>
                                <dd class="font-medium text-gray-900">{{ moneyShort($order->subtotal) }}</dd>
                            </div>
                            @if ($order->discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <dt>{{ __('Discount') }}</dt>
                                    <dd class="font-medium">-{{ moneyShort($order->discount) }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-gray-500">{{ __('Delivery') }}</dt>
                                <dd class="font-medium text-gray-900">{{ moneyShort($order->delivery_charge) }}</dd>
                            </div>
                            <div class="border-t pt-2 flex justify-between">
                                <dt class="font-semibold text-gray-900">{{ __('Total') }}</dt>
                                <dd class="font-bold text-lg text-gray-900">{{ moneyShort($order->total) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('shop') }}" class="inline-flex items-center px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        {{ __('Continue Shopping') }}
                    </a>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection