@extends('layouts.app')

@section('title', __('Order Confirmed') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    @livewireLazy('storefront.cart-drawer')

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Success Animation -->
            <div class="text-center mb-8">
                <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Order Confirmed!') }}</h1>
                <p class="text-gray-500 mt-2">{{ __('Thank you for your order. We\'ll process it shortly.') }}</p>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-xl border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Order Details') }}</h2>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                        {{ $order->status->label() }}
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
                        <p class="font-medium text-gray-900">{{ $order->payment_method->label() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">{{ __('Payment Status') }}</p>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $order->payment_status === \App\Enums\PaymentStatus::Paid ? 'bg-green-100 text-green-800' : ($order->payment_status === \App\Enums\PaymentStatus::AwaitingVerification ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $order->payment_status->label() }}
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

                <div class="border-t pt-4">
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

                <div class="border-t pt-4 mt-4">
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
                            <dt class="font-semibold text-gray-900">{{ __('Total Paid') }}</dt>
                            <dd class="font-bold text-lg text-gray-900">{{ moneyShort($order->total) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-xl border border-blue-100 p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">{{ __('What happens next?') }}</h3>
                <ol class="space-y-2 text-sm text-blue-700">
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs">1</span> {{ __('We\'ll review and confirm your order within 24 hours.') }}</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs">2</span> {{ __('You\'ll receive an SMS with tracking details once shipped.') }}</li>
                    <li class="flex items-center gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs">3</span> {{ __('Delivery within 1-2 days (Dhaka) or 2-5 days (outside Dhaka).') }}</li>
                </ol>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('track.order') }}" class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-center font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    {{ __('Track Order') }}
                </a>
                <a href="{{ route('shop') }}" class="flex-1 px-6 py-3 bg-primary text-white rounded-lg text-center font-medium hover:bg-primary/90 transition-colors">
                    {{ __('Continue Shopping') }}
                </a>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                {{ __('Need help? Contact us at') }} <a href="https://wa.me/{{ settings('whatsapp_number') }}" class="text-primary hover:underline" target="_blank">{{ __('WhatsApp') }}</a>
            </p>
        </div>
    </main>
</div>
@endsection