@extends('layouts.app')

@section('title', __('Cart') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ __('Shopping Cart') }}</h1>

        @if($items->isEmpty())
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                </svg>
                <h2 class="text-xl font-medium text-gray-900 mb-2">{{ __('Your cart is empty') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('Looks like you haven\'t added any products yet.') }}</p>
                <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                    {{ __('Continue Shopping') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Cart Items') }} ({{ $items->count() }})</h2>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($items as $item)
                                <div class="flex gap-4 p-6">
                                    <a href="{{ route('product.show', $item['variant']->product->slug) }}" class="relative w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                        @if($item['variant']->product->getFirstImageUrl('thumb'))
                                            <img src="{{ $item['variant']->product->getFirstImageUrl('thumb') }}" alt="{{ $item['variant']->product->getDisplayNameAttribute() }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                            </div>
                                        @endif>
                                    </a>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <a href="{{ route('product.show', $item['variant']->product->slug) }}" class="font-medium text-gray-900 hover:text-primary">{{ $item['variant']->product->getDisplayNameAttribute() }}</a>
                                                <div class="flex items-center gap-3 mt-1 text-sm text-gray-600">
                                                    <span>{{ $item['variant']->getVariantLabel() }}</span>
                                                    @if($item['variant']->color)
                                                        <span class="w-4 h-4 rounded-full border" style="background-color: {{ $item['variant']->color->hex }}"></span>
                                                    @endif>
                                                </div>
                                            </div>
                                            <span class="font-semibold text-gray-900 whitespace-nowrap">{{ number_format($item['subtotal'] / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                                        </div>

                                        <div class="flex items-center justify-between mt-4">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button type="button" wire:click="$dispatch('update-cart', { variantId: {{ $item['variant']->id }}, quantity: {{ $item['quantity'] - 1 }} })" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg" {{ $item['quantity'] <= 1 ? 'disabled' : '' }} aria-label="{{ __('Decrease quantity') }}">-</button>
                                                <input type="number" value="{{ $item['quantity'] }}" min="1" max="{{ $item['variant']->stock }}" class="w-12 text-center border-x border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" onchange="Livewire.dispatch('update-cart', { variantId: {{ $item['variant']->id }}, quantity: parseInt(this.value) })" aria-label="{{ __('Quantity') }}">
                                                <button type="button" wire:click="$dispatch('update-cart', { variantId: {{ $item['variant']->id }}, quantity: {{ $item['quantity'] + 1 }} })" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg" {{ $item['quantity'] >= $item['variant']->stock ? 'disabled' : '' }} aria-label="{{ __('Increase quantity') }}">+</button>
                                            </div>

                                            <button type="button" wire:click="$dispatch('remove-from-cart', { variantId: {{ $item['variant']->id }} })" class="text-sm text-gray-500 hover:text-red-600" aria-label="{{ __('Remove') }}">{{ __('Remove') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 text-primary hover:underline font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            {{ __('Continue Shopping') }}
                        </a>
                        <button type="button" wire:click="$dispatch('clear-cart')" class="text-gray-500 hover:text-red-600 text-sm">{{ __('Clear Cart') }}</button>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Order Summary') }}</h2>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Subtotal') }}</dt>
                                <dd class="font-medium text-gray-900">{{ number_format($subtotal / 100, 2) }} {{ config('store.currency', 'BDT') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Shipping') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $subtotal >= 200000 ? __('Free') : number_format(8000 / 100, 2) }} {{ config('store.currency', 'BDT') }}</dd>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between">
                                <dt class="font-semibold text-gray-900">{{ __('Total') }}</dt>
                                <dd class="font-bold text-gray-900 text-lg">{{ number_format(($subtotal + ($subtotal >= 200000 ? 0 : 8000)) / 100, 2) }} {{ config('store.currency', 'BDT') }}</dd>
                            </div>
                        </dl>

                        @if($subtotal < 200000)
                            <p class="mt-4 text-sm text-gray-600">{{ __('Add {{ amount }} more for free delivery!', ['amount' => number_format((200000 - $subtotal) / 100, 2)]) }}</p>
                        @else
                            <p class="mt-4 text-sm text-green-600">{{ __('You qualify for free delivery!') }}</p>
                        @endif>

                        <a href="{{ route('checkout') }}" class="mt-6 w-full bg-primary text-white py-3 px-6 rounded-lg font-medium text-center hover:bg-primary/90 transition-colors block">{{ __('Proceed to Checkout') }}</a>

                        <div class="mt-4 flex items-center gap-2 text-center text-sm text-gray-500">
                            <svg class="w-5 h-5 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Secure checkout') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-center text-sm text-gray-500">
                            <svg class="w-5 h-5 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Cash on delivery available') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-center text-sm text-gray-500">
                            <svg class="w-5 h-5 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Easy returns') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @livewire('storefront.cart-drawer')
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('update-cart', (event) => {
            fetch('{{ route('cart.update', ':variantId') }}'.replace(':variantId', event.detail.variantId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ quantity: event.detail.quantity }),
            });
        });

        Livewire.on('remove-from-cart', (event) => {
            fetch('{{ route('cart.remove', ':variantId') }}'.replace(':variantId', event.detail.variantId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            });
        });

        Livewire.on('clear-cart', () => {
            fetch('{{ route('cart.clear') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            });
        });

        Livewire.on('add-to-cart', (event) => {
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ variant_id: event.detail.variantId, quantity: event.detail.quantity }),
            }).then(() => {
                @this.call('open');
            });
        });
    });
</script>
@endpush