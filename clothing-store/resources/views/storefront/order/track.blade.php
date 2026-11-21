@extends('layouts.app')

@section('title', __('Track Order') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Track Your Order') }}</h1>
            <p class="text-gray-600 mb-8">{{ __('Enter your order number and phone number to track your order.') }}</p>

            <form method="POST" action="{{ route('track-order.post') }}" class="bg-white rounded-xl border border-gray-100 p-6 mb-8">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="order_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order Number') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="order_number" id="order_number" required autocomplete="off" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="ORD-XXXXXX">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" required autocomplete="tel" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="+880 1XXX XXXXXX">
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-primary/90 transition-colors">{{ __('Track Order') }}</button>
                </div>
            </form>

            @if(isset($order))
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Order Details') }}</h2>

                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">{{ __('Order Number') }}</span>
                                <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">{{ __('Status') }}</span>
                                <p class="font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                           ($order->status === 'processing' ? 'bg-purple-100 text-purple-800' :
                                           ($order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800' :
                                           ($order->status === 'delivered' ? 'bg-green-100 text-green-800' :
                                           'bg-red-100 text-red-800')))) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600">{{ __('Date') }}</span>
                                <p class="font-medium text-gray-900">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">{{ __('Total') }}</span>
                                <p class="font-bold text-gray-900">{{ number_format($order->total / 100, 2) }} {{ config('store.currency', 'BDT') }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="font-medium text-gray-900 mb-3">{{ __('Shipping Address') }}</h3>
                            <address class="text-sm text-gray-600 not-italic">
                                {{ $order->shipping_name }}<br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_area }}, {{ $order->shipping_district }}<br>
                                {{ $order->shipping_phone }}
                            </address>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Order Items') }}</h3>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex gap-3">
                                    <div class="relative w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                        @if($item->product_image)
                                            <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                            </div>
                                        @endif>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $item->product_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $item->variant_label }} × {{ $item->quantity }}</p>
                                        <p class="font-medium text-gray-900">{{ number_format($item->subtotal / 100, 2) }} {{ config('store.currency', 'BDT') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($order->tracking_number || $order->courier)
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="font-medium text-gray-900 mb-3">{{ __('Tracking Information') }}</h3>
                            <div class="space-y-2 text-sm">
                                @if($order->courier)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Courier') }}</span>
                                        <span class="font-medium text-gray-900">{{ $order->courier }}</span>
                                    </div>
                                @endif
                                @if($order->tracking_number)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Tracking Number') }}</span>
                                        <span class="font-mono font-medium text-gray-900">{{ $order->tracking_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @livewire('storefront.cart-drawer')
</div>
@endsection