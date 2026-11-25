@php
    $activeVariants = $product->getActiveVariantsWithStock();
    $firstVariantId = $activeVariants->first()?->id ?? 0;
    $productUrl = route('product.show', $product->slug);
@endphp
<article class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300">
    <div class="aspect-square relative bg-gray-50 overflow-hidden">
        <a href="{{ $productUrl }}" class="block w-full h-full" aria-label="{{ $product->getDisplayNameAttribute() }}">
            @if($product->getFirstImageUrl('card'))
                <img src="{{ $product->getFirstImageUrl('card') }}"
                     alt="{{ $product->getDisplayNameAttribute() }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                </div>
            @endif
        </a>

        @if($product->is_new)
            <span class="absolute top-2 left-2 bg-primary text-white text-xs font-medium px-2 py-1 rounded">{{ __('New') }}</span>
        @endif

        @if($product->compare_at_price && $product->price < $product->compare_at_price)
            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-medium px-2 py-1 rounded">
                -{{ round(($product->compare_at_price - $product->price) / $product->compare_at_price * 100) }}%
            </span>
        @endif

        <div class="absolute bottom-2 left-2 right-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-200 flex gap-2 justify-center">
            <form method="POST" action="{{ route('cart.add') }}" class="inline">
                @csrf
                <input type="hidden" name="variant_id" value="{{ $firstVariantId }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        class="bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-medium shadow-lg hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-label="{{ __('Add to cart') }}: {{ $product->getDisplayNameAttribute() }}"
                        @disabled($activeVariants->isEmpty())>
                    {{ __('Add to Cart') }}
                </button>
            </form>
            <a href="{{ $productUrl }}"
               class="bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-medium shadow-lg hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary inline-flex items-center"
               aria-label="{{ __('Quick view') }}: {{ $product->getDisplayNameAttribute() }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
        </div>
    </div>

    <div class="p-4">
        <a href="{{ $productUrl }}" class="block" aria-label="{{ $product->getDisplayNameAttribute() }}">
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ $product->category?->getDisplayNameAttribute() }}</p>
            <h3 class="font-medium text-gray-900 mb-2 line-clamp-1">{{ $product->getDisplayNameAttribute() }}</h3>

            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-900">{{ number_format($product->price / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                @if($product->compare_at_price && $product->price < $product->compare_at_price)
                    <span class="text-sm text-gray-500 line-through">{{ number_format($product->compare_at_price / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                @endif
            </div>

            @if($activeVariants->isEmpty())
                <p class="text-xs text-red-600 mt-2">{{ __('product.out_of_stock') }}</p>
            @elseif($activeVariants->where('stock', '<=', 5)->isNotEmpty())
                <p class="text-xs text-amber-600 mt-2">{{ __('Low stock') }}</p>
            @endif
        </a>
    </div>
</article>
