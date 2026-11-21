<div x-show="isOpen" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform translate-x-full" class="fixed inset-0 z-50 lg:hidden" @click.outside="close()" @cart-drawer-opened.window="isOpen = true" @cart-updated.window="refreshCart()" x-data="{ isOpen: @entangle($isOpen).defer }">
    <div class="fixed inset-0 bg-black/50" @click="close()"></div>
    
    <div class="fixed right-0 top-0 h-full w-full max-w-sm bg-white shadow-xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Shopping Cart') }}</h2>
            <button @click="close()" class="p-2 text-gray-400 hover:text-gray-600" aria-label="{{ __('Close cart') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            @if(empty($cartItems))
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                    </svg>
                    <p class="text-gray-500">{{ __('Your cart is empty') }}</p>
                    <a href="{{ route('shop') }}" class="btn-primary mt-4 inline-block">{{ __('Continue Shopping') }}</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($cartItems as $variantId => $quantity)
                        <div class="flex gap-3" wire:key="cart-item-{{ $variantId }}">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ __('Product') }} #{{ $variantId }}</h4>
                                <p class="text-sm text-gray-500">{{ __('Qty') }}: {{ $quantity }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button wire:click="updateQuantity({{ $variantId }}, {{ $quantity - 1 }})" class="p-1 text-gray-400 hover:text-gray-600" aria-label="{{ __('Decrease quantity') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                    </button>
                                    <span class="w-8 text-center text-sm">{{ $quantity }}</span>
                                    <button wire:click="updateQuantity({{ $variantId }}, {{ $quantity + 1 }})" class="p-1 text-gray-400 hover:text-gray-600" aria-label="{{ __('Increase quantity') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </button>
                                    <button wire:click="remove({{ $variantId }})" class="ml-auto p-1 text-gray-400 hover:text-red-500" aria-label="{{ __('Remove item') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(!empty($cartItems))
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-between text-sm mb-3">
                <span class="text-gray-600">{{ __('Subtotal') }}</span>
                <span class="font-medium text-gray-900">৳{{ number_format($subtotal) }}</span>
            </div>
            <div class="flex justify-between text-sm mb-3">
                <span class="text-gray-600">{{ __('Delivery') }}</span>
                <span class="font-medium text-gray-900">{{ __('Calculated at checkout') }}</span>
            </div>
            <div class="flex justify-between text-base font-semibold text-gray-900 mb-4">
                <span>{{ __('Total') }}</span>
                <span>৳{{ number_format($subtotal) }}</span>
            </div>
            <a href="{{ route('checkout') }}" class="btn-primary w-full text-center">{{ __('Proceed to Checkout') }}</a>
            <a href="{{ route('shop') }}" class="btn-secondary w-full text-center mt-2">{{ __('Continue Shopping') }}</a>
        </div>
        @endif
    </div>
</div>