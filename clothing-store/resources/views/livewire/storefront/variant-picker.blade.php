<div class="space-y-6">
    <h2 class="sr-only">{{ $product->getDisplayNameAttribute() }}</h2>

    <div>
        <span id="color-label" class="block text-sm font-medium text-gray-900 mb-2">{{ __('product.color') }}</span>
        <div class="flex flex-wrap gap-2" role="radiogroup" aria-labelledby="color-label">
            @foreach($this->availableColorOptions() as $color)
                <button type="button"
                    wire:click="$set('selectedColorId', {{ $color->id }})"
                    class="relative px-3 py-2 border-2 rounded-lg text-sm font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2
                        {{ $selectedColorId == $color->id ? 'border-primary bg-primary/5 text-primary' : 'border-gray-300 hover:border-gray-400' }}"
                    role="radio"
                    aria-checked="{{ $selectedColorId == $color->id ? 'true' : 'false' }}"
                    style="background-color: {{ $selectedColorId == $color->id ? $color->hex . '20' : 'transparent' }}">
                    <span class="w-4 h-4 rounded-full mr-2 inline-block border border-gray-300" style="background-color: {{ $color->hex }}"></span>
                    {{ $color->display_name }}
                </button>
            @endforeach
        </div>
    </div>

    <div>
        <span id="size-label" class="block text-sm font-medium text-gray-900 mb-2">{{ __('product.size') }}</span>
        <div class="flex flex-wrap gap-2" role="radiogroup" aria-labelledby="size-label">
            @foreach($availableSizes as $size)
                @php
                    $isAvailable = $this->sizeAvailable($size);
                    $isLowStock = $this->sizeLowStock($size);
                @endphp
                <button type="button"
                    wire:click="$set('selectedSize', '{{ $size }}')"
                    class="w-12 h-12 border-2 rounded-lg text-sm font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2
                        {{ $selectedSize == $size ? 'border-primary bg-primary/5 text-primary' : ($isAvailable ? 'border-gray-300 hover:border-gray-400' : 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed line-through') }}"
                    role="radio"
                    aria-checked="{{ $selectedSize == $size ? 'true' : 'false' }}"
                    {{ !$isAvailable ? 'disabled' : '' }}>
                    {{ $size }}
                    @if($isAvailable && $isLowStock)
                        <span class="block text-xs text-amber-600 mt-1">{{ __('Low stock') }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <div wire:show="selectedVariantId" wire:transition class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-lg font-bold text-gray-900">
                    {{ number_format($variantPicker->getSelectedVariantPrice() / 100, 2) }} {{ config('store.currency', 'BDT') }}
                </span>
                @if($product->compare_at_price && $variantPicker->getSelectedVariantPrice() < $product->compare_at_price)
                    <span class="ml-2 text-sm text-gray-500 line-through">
                        {{ number_format($product->compare_at_price / 100, 2) }} {{ config('store.currency', 'BDT') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center border border-gray-300 rounded-lg">
                <button type="button" wire:click="decrementQuantity" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-l-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-primary" wire:loading.attr="disabled" aria-label="{{ __('messages.decrease_quantity') }}">-</button>
                <input type="number" wire:model.defer="quantity" min="1" max="{{ $variantPicker->getSelectedVariantStock() }}" class="w-16 text-center border-x border-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary" aria-label="{{ __('messages.quantity') }}">
                <button type="button" wire:click="incrementQuantity" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-r-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-primary" wire:loading.attr="disabled" aria-label="{{ __('messages.increase_quantity') }}">+</button>
            </div>

            <div class="text-sm text-gray-600" aria-live="polite">
                {{ __('messages.in_stock', ['count' => $variantPicker->getSelectedVariantStock()]) }}
            </div>
        </div>

        @if($variantPicker->isLowStock())
<div class="flex items-center gap-2 text-amber-600 text-sm" aria-live="polite">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ __('messages.only_left', ['count' => $variantPicker->getSelectedVariantStock()]) }}</span>
            </div>
        @endif

        @if($variantPicker->isOutOfStock())
            <div class="flex items-center gap-2 text-red-600 text-sm" role="alert">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293-1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ __('product.out_of_stock') }}</span>
            </div>
        @endif

        <button type="button"
            wire:click="addToCart"
            class="w-full bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-primary/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 transition-colors
                {{ $variantPicker->isOutOfStock() ? 'opacity-50 cursor-not-allowed' : '' }}"
            {{ $variantPicker->isOutOfStock() ? 'disabled' : '' }}>
            @if($variantPicker->isOutOfStock())
                {{ __('product.out_of_stock') }}
            @else
                {{ __('product.add_to_cart') }}
            @endif
        </button>
    </div>

    <div wire:show="!selectedVariantId" wire:transition class="text-center py-8 text-gray-500">
        <p>{{ __('product.select_variant_error') }}</p>
    </div>

    <div class="border-t border-gray-200 pt-6">
        <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('messages.Delivery') }}</h3>
        <div class="space-y-2 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                <span>{{ __('messages.order_by_2pm') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                <span>{{ __('messages.free_delivery_over') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                <span>{{ __('messages.easy_returns') }}</span>
            </div>
        </div>
    </div>
</div>
