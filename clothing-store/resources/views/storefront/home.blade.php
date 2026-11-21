@extends('layouts.app')

@section('title', config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">

    @if($banners->isNotEmpty())
        <section class="relative" aria-label="{{ __('Hero banner') }}">
            <div class="relative h-72 md:h-96">
                @foreach($banners as $banner)
                    <div class="absolute inset-0" {{ $loop->first ? '' : 'style="display: none"' }}>
                        @if($banner->image_path)
                            <img src="{{ asset('storage/' . $banner->image_path) }}"
                                 alt="{{ $banner->title }}"
                                 class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-black/40"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="container mx-auto px-4 text-center text-white">
                                <h1 class="text-4xl md:text-6xl font-bold mb-4">{{ $banner->title }}</h1>
                                @if($banner->subtitle)
                                    <p class="text-xl md:text-2xl mb-6 max-w-2xl mx-auto">{{ $banner->subtitle }}</p>
                                @endif
                                @if($banner->button_text && $banner->button_link)
                                    <a href="{{ $banner->button_link }}" class="inline-flex items-center gap-2 bg-white text-gray-900 px-8 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                                        {{ $banner->button_text }}
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    @foreach($banners as $index => $banner)
                        <button type="button" class="w-2 h-2 rounded-full transition-all {{ $loop->first ? 'bg-white' : 'bg-white/50' }}" aria-label="{{ __('Slide :number', ['number' => $index + 1]) }}"></button>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <main class="container mx-auto px-4 py-8">
        @if($categories->isNotEmpty())
            <section aria-label="{{ __('Shop by Category') }}">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Shop by Category') }}</h2>
                    <a href="{{ route('categories') }}" class="text-primary hover:underline text-sm font-medium">{{ __('View All') }}</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($categories as $category)
                        <a href="{{ route('shop', ['category' => $category->slug]) }}" class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if($category->image_path)
                                    <img src="{{ asset('storage/' . $category->image_path) }}"
                                         alt="{{ $category->getDisplayNameAttribute() }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 text-center">
                                <h3 class="font-medium text-gray-900">{{ $category->getDisplayNameAttribute() }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $category->activeChildren->count() }} {{ __('subcategories') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($featuredProducts->isNotEmpty())
            <section aria-label="{{ __('Featured Products') }}" class="mt-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Featured Products') }}</h2>
                    <a href="{{ route('shop') }}" class="text-primary hover:underline text-sm font-medium">{{ __('View All') }}</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>
        @endif

        @if($newProducts->isNotEmpty())
            <section aria-label="{{ __('New Arrivals') }}" class="mt-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('New Arrivals') }}</h2>
                    <a href="{{ route('shop') }}?sort=newest" class="text-primary hover:underline text-sm font-medium">{{ __('View All') }}</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($newProducts as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-16 bg-white rounded-2xl border border-gray-100 p-8 md:p-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-4">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l-1.14-1.14-2.83 2.83-1.95-1.95-1.41 1.41L18 16.17l4.44-4.44z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Free Delivery') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('On orders over ৳2,000') }}</p>
                </div>
                <div class="p-4">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm-1-16v8.5l6.5 3.5-.75 1.42-6.5-3.5v-9.92z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Fast Shipping') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('Same-day dispatch in Dhaka') }}</p>
                </div>
                <div class="p-4">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Secure Payment') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('Cash on delivery available') }}</p>
                </div>
</div>
        </div>
    </main>
</div>
@endsection