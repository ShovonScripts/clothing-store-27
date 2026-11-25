@extends('layouts.app')

@section('title', __('global.cart') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    @livewireLazy('storefront.cart-drawer')

    <main class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Shopping Cart') }} ({{ $count }})</h1>
            <p class="text-gray-500 mt-1">{{ __('Review your items before checkout') }}</p>
        </div>

        @if ($items->isEmpty())
            <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                </svg>
                <h2 class="mt-4 text-xl font-medium text-gray-900">{{ __('Your cart is empty') }}</h2>
                <p class="mt-2 text-gray-500">{{ __('Looks like you haven\'t added any items yet.') }}</p>
                <a href="{{ route('shop') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Continue Shopping') }}
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('global.product') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Size / Color') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $item['image'] ?? asset('images/placeholder.svg') }}" alt="{{ $item['product_name'] }}" class="w-16 h-16 object-cover rounded-lg">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900">{{ $item['product_name'] }}</h3>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm text-gray-500">{{ $item['variant_size'] }} / {{ $item['variant_color'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900">{{ moneyShort($item['price']) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="updateQty({{ $item['variant_id'] }}, {{ $item['qty'] - 1 }})" class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-50" aria-label="Decrease">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                            </button>
                                            <span class="w-8 text-center text-sm font-medium">{{ $item['qty'] }}</span>
                                            <button wire:click="updateQty({{ $item['variant_id'] }}, {{ $item['qty'] + 1 }})" class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-50" aria-label="Increase">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900">{{ moneyShort($item['price'] * $item['qty']) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <button wire:click="remove({{ $item['variant_id'] }})" class="text-gray-400 hover:text-red-500" aria-label="Remove">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t bg-gray-50">
                    <div class="flex justify-end gap-4">
                        <button wire:click="clear" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('Clear Cart') }}
                        </button>
                        <a href="{{ route('checkout') }}" class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                            {{ __('Proceed to Checkout') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6 lg:ml-64 lg:w-1/3">
                <div class="bg-white rounded-xl border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Order Summary') }}</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('Subtotal') }}</dt>
                            <dd class="font-medium text-gray-900">{{ moneyShort($subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">{{ __('Estimated Delivery') }}</dt>
                            <dd class="font-medium text-gray-900">{{ __('Calculated at checkout') }}</dd>
                        </div>
                        <div class="border-t pt-3 flex justify-between">
                            <dt class="font-medium text-gray-900">{{ __('Total') }}</dt>
                            <dd class="font-bold text-lg text-gray-900">{{ moneyShort($subtotal) }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-xs text-gray-500 text-center">{{ __('Delivery charges will be calculated at checkout based on your location.') }}</p>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection