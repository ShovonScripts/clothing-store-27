@extends('layouts.app')

@section('title', __('Search') . ': ' . $query . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto mb-8">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <input type="text"
                       name="q"
                       value="{{ $query }}"
                       placeholder="{{ __('Search products...') }}"
                       class="w-full bg-white border border-gray-300 rounded-lg px-6 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent pr-16"
                       aria-label="{{ __('Search products') }}"
                       autofocus>
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
        </div>

        @if($query)
            <p class="text-gray-600 mb-6">{{ $products->total() }} {{ __('results for') }} "<span class="font-medium text-gray-900">{{ $query }}</span>"</p>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="lg:w-64 flex-shrink-0 hidden lg:block">
                @livewire('storefront.product-filters', ['categorySlug' => ''], key('product-filters-search'))
            </aside>

            <main class="flex-1 min-w-0">
                @if($products->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="product-grid">
                        @foreach($products as $product)
                            @include('storefront.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-center">
                        {{ $products->links('vendor.pagination.tailwind') }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-xl font-medium text-gray-900 mb-2">{{ __('No products found') }}</h2>
                        <p class="text-gray-600 mb-6">{{ __('Try different keywords or browse our categories.') }}</p>
                        <a href="{{ route('shop') }}" class="text-primary hover:underline font-medium">{{ __('Browse all products') }}</a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    @livewire('storefront.cart-drawer')
</div>
@endsection