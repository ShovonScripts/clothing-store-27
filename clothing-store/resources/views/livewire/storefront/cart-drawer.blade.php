<div wire:show="isOpen" wire:transition.enter="transition ease-out duration-300" wire:transition.enter-start="opacity-0 transform translate-x-full" wire:transition.enter-end="opacity-100 transform translate-x-0" wire:transition.leave="transition ease-in duration-200" wire:transition.leave-start="opacity-100 transform translate-x-0" wire:transition.leave-end="opacity-0 transform translate-x-full" class="fixed inset-0 z-50 lg:hidden" @click.outside="close()" @cart-drawer-opened.window="isOpen = true">
    <div class="fixed inset-0 bg-black/50" @click="close()"></div>

    <div class="fixed right-0 top-0 h-full w-full max-w-sm bg-white shadow-xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Cart') }} ({{ $cartCount }})</h2>
            <button type="button" wire:click="close" class="p-2 text-gray-500 hover:text-gray-700" aria-label="{{ __('Close cart') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            @if(empty($cartItems))
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                    </svg>
                    <p class="text-gray-600">{{ __('Your cart is empty') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="flex gap-3">
                            <a href="{{ route('product.show', $item['variant']->product->slug) }}" class="relative w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                @if($item['variant']->product->getFirstImageUrl('thumb'))
                                    <img src="{{ $item['variant']->product->getFirstImageUrl('thumb') }}" alt="{{ $item['variant']->product->getDisplayNameAttribute() }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                    </div>
                                @endif>
                            </a>

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('product.show', $item['variant']->product->slug) }}" class="font-medium text-gray-900 hover:text-primary line-clamp-1">{{ $item['variant']->product->getDisplayNameAttribute() }}</a>
                                <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                                    <span>{{ $item['variant']->getVariantLabel() }}</span>
                                    @if($item['variant']->color)
                                        <span class="w-3 h-3 rounded-full border" style="background-color: {{ $item['variant']->color->hex }}"></span>
                                    @endif>
                                </div>

                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button type="button" wire:click="updateQuantity({{ $item['variant']->id }}, {{ $item['quantity'] - 1 }})" class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg" {{ $item['quantity'] <= 1 ? 'disabled' : '' }} aria-label="{{ __('Decrease quantity') }}">-</button>
                                        <span class="w-8 text-center border-x border-gray-300">{{ $item['quantity'] }}</span>
                                        <button type="button" wire:click="updateQuantity({{ $item['variant']->id }}, {{ $item['quantity'] + 1 }})" class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg" {{ $item['quantity'] >= $item['variant']->stock ? 'disabled' : '' }} aria-label="{{ __('Increase quantity') }}">+</button>
                                    </div>

                                    <span class="font-semibold text-gray-900">{{ number_format($item['subtotal'] / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                                </div>

                                <button type="button" wire:click="remove({{ $item['variant']->id }})" class="mt-2 text-xs text-gray-500 hover:text-red-600">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(!empty($cartItems))
            <div class="border-t border-gray-100 p-4 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('Subtotal') }}</span>
                    <span class="font-medium text-gray-900">{{ number_format($subtotal / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('Shipping') }}</span>
                    <span class="font-medium text-gray-900">{{ $subtotal >= $freeShippingThreshold ? __('Free') : number_format($shipping / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                </div>

                @if($subtotal < $freeShippingThreshold)
                    <p class="text-xs text-amber-600 text-center">{{ __('Add :amount more for free delivery!', ['amount' => number_format(($freeShippingThreshold - $subtotal) / 100, 2)]) }}</p>
                @else
                    <p class="text-xs text-green-600 text-center">{{ __('You qualify for free delivery!') }}</p>
                @endif>

                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                    <span>{{ __('Total') }}</span>
                    <span>{{ number_format($total / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                </div>

                <a href="{{ route('cart.page') }}" class="block w-full bg-gray-100 text-gray-900 py-3 px-4 rounded-lg font-medium text-center hover:bg-gray-200 transition-colors">{{ __('View Cart') }}</a>

                <a href="{{ route('checkout') }}" class="block w-full bg-primary text-white py-3 px-4 rounded-lg font-medium text-center hover:bg-primary/90 transition-colors">{{ __('Checkout') }}</a>
            </div>
        @endif
    </div>
</div>