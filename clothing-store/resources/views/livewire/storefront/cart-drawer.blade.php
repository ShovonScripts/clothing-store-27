@if ($isOpen)
<div wire:ignore.self class="fixed inset-0 z-50 lg:hidden" wire:click="close" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50 transition-opacity" aria-hidden="true"></div>

    <div class="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-xl transition-transform" wire:click="$event.stopPropagation()" x-trap.inert="true" role="dialog" aria-modal="true" aria-labelledby="cart-title">
        <div class="flex items-center justify-between p-4 border-b">
            <h2 id="cart-title" class="text-lg font-semibold text-gray-900">{{ __('global.cart') }} ({{ $count }})</h2>
            <button wire:click="close" class="text-gray-400 hover:text-gray-600" aria-label="{{ __('cart.close_cart') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto max-h-[calc(100vh-200px)] p-4">
            @if ($items->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                    </svg>
                    <p class="mt-2 text-gray-500">{{ __('Your cart is empty') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <div class="flex gap-3">
                            <img src="{{ $item['image'] ?? asset('images/placeholder.svg') }}" alt="{{ $item['product_name'] }}" class="w-16 h-16 object-cover rounded-lg">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item['product_name'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $item['variant_size'] }} / {{ $item['variant_color'] }}</p>
                                <p class="text-sm font-medium text-primary">{{ moneyShort($item['price']) }}</p>

                                <div class="flex items-center gap-2 mt-2">
                                    <button wire:click="updateQty({{ $item['variant_id'] }}, {{ $item['qty'] - 1 }})" class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-50" aria-label="Decrease quantity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                    </button>
                                    <span class="w-8 text-center text-sm">{{ $item['qty'] }}</span>
                                    <button wire:click="updateQty({{ $item['variant_id'] }}, {{ $item['qty'] + 1 }})" class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-50" aria-label="Increase quantity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </button>
                                </div>
                            </div>
                            <button wire:click="remove({{ $item['variant_id'] }})" class="text-gray-400 hover:text-red-500" aria-label="Remove item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('Subtotal') }}</span>
                        <span class="font-medium text-gray-900">{{ moneyShort($subtotal) }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ __('Delivery charges calculated at checkout') }}</p>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('cart.page') }}" wire:click="close" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('View Cart') }}
                        </a>
                        <a href="{{ route('checkout') }}" wire:click="close" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg text-center text-sm font-medium hover:bg-primary/90">
                            {{ __('global.checkout') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('open-cart', () => {
        const drawer = document.querySelector('[wire\\:id]');
        if (drawer && drawer._livewire) {
            drawer._livewire.call('open');
        }
    });
</script>
@endif