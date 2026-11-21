@extends('layouts.app')

@section('title', config('store.name', '{BRAND}'))

@section('content')
<div class="space-y-12">
    <!-- Hero Section -->
    <section class="relative overflow-hidden" aria-labelledby="hero-heading">
        <div class="container mx-auto px-4 py-12 lg:py-20">
            <div class="max-w-3xl mx-auto text-center">
                <h1 id="hero-heading" class="text-3xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                    {{ __('Men\'s Fashion for the Modern Gentleman') }}
                </h1>
                <p class="text-lg lg:text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    {{ __('Discover premium quality shirts, t-shirts, panjabis, and more. Designed for Bangladesh, delivered to your door.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('shop') }}" class="btn-primary">
                        {{ __('Shop Now') }}
                    </a>
                    <a href="{{ route('categories') }}" class="btn-secondary">
                        {{ __('Browse Categories') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Strip -->
    <section class="bg-gray-50 py-8" aria-labelledby="trust-heading">
        <div class="container mx-auto px-4">
            <h2 id="trust-heading" class="sr-only">{{ __('Why Choose Us') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ __('Cash on Delivery') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Pay when you receive') }}</p>
                </div>
                <div class="p-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ __('bKash Payment') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Secure manual verification') }}</p>
                </div>
                <div class="p-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ __('Nationwide Delivery') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('All 64 districts covered') }}</p>
                </div>
                <div class="p-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ __('Easy Returns') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('3-day exchange policy') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-12 lg:py-16" aria-labelledby="categories-heading">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 id="categories-heading" class="text-2xl lg:text-3xl font-bold text-gray-900">{{ __('Shop by Category') }}</h2>
                <a href="{{ route('categories') }}" class="text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                    {{ __('View All') }} <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4" role="list">
                @foreach(['T-Shirts', 'Polo Shirts', 'Shirts', 'Panjabi', 'Pants & Jeans', 'Hoodies', 'Jackets', 'Innerwear', 'Accessories'] as $category)
                <a href="{{ route('shop') }}?category={{ Str::slug($category) }}" class="card group p-6 text-center" role="listitem">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                        <svg class="w-10 h-10 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17a.002.002 0 11-.004-.002z" />
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900">{{ __($category) }}</h3>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products Placeholder -->
    <section class="py-12 lg:py-16 bg-gray-50" aria-labelledby="featured-heading">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 id="featured-heading" class="text-2xl lg:text-3xl font-bold text-gray-900">{{ __('New Arrivals') }}</h2>
                <a href="{{ route('shop') }}?sort=newest" class="text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                    {{ __('View All') }} <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" role="list">
                @for($i = 1; $i <= 4; $i++)
                <article class="card group" role="listitem">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="absolute top-2 left-2">
                            <span class="badge bg-accent text-white">{{ __('New') }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 mb-1 line-clamp-1">{{ __('Premium Cotton T-Shirt') }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ __('Black / Navy / White') }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-900">৳{{ number_format(1250) }}</span>
                            <button class="btn-primary text-sm px-3 py-1.5" aria-label="{{ __('Add to cart') }}">
                                {{ __('Add') }}
                            </button>
                        </div>
                    </div>
                </article>
                @endfor
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 lg:py-16" aria-labelledby="cta-heading">
        <div class="container mx-auto px-4">
            <div class="bg-primary text-white rounded-2xl p-8 lg:p-12 text-center">
                <h2 id="cta-heading" class="text-2xl lg:text-3xl font-bold mb-4">{{ __('Ready to Upgrade Your Wardrobe?') }}</h2>
                <p class="text-lg lg:text-xl text-primary/80 mb-8 max-w-2xl mx-auto">
                    {{ __('Join thousands of satisfied customers. Free delivery on orders over ৳') }} {{ number_format(settings('free_delivery_over', 0)) }}.
                </p>
                <a href="{{ route('shop') }}" class="btn-accent inline-flex">
                    {{ __('Start Shopping') }}
                </a>
            </div>
        </div>
    </section>
</div>
@endsection