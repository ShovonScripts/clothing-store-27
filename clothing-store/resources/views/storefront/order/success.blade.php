@extends('layouts.app')

@section('title', __('Order Confirmed') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-md mx-auto text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Order Confirmed!') }}</h1>
            <p class="text-gray-600 mb-6">{{ __('Thank you for your order. We\'ll send a confirmation to your phone/email shortly.') }}</p>

            <div class="bg-white rounded-xl border border-gray-100 p-6 mb-6 text-left">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600">{{ __('Order Number') }}</span>
                    <span class="font-mono font-bold text-lg text-gray-900">{{ $orderNumber }}</span>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ __('Status') }}</span>
                    <span class="font-medium text-gray-900">{{ __('Confirmed') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ __('Payment') }}</span>
                    <span class="font-medium text-gray-900">{{ __('Cash on Delivery') }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('home') }}" class="block w-full bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-primary/90 transition-colors">{{ __('Continue Shopping') }}</a>
                <a href="{{ route('track-order') }}" class="block w-full bg-gray-100 text-gray-900 py-3 px-6 rounded-lg font-medium hover:bg-gray-200 transition-colors">{{ __('Track Order') }}</a>
            </div>

            <p class="mt-6 text-sm text-gray-500">{{ __('You can track your order using the order number above and your phone number.') }}</p>
        </div>
    </div>

    @livewire('storefront.cart-drawer')
</div>
@endsection