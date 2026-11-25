@extends('layouts.app')

@section('title', __('All Categories') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ __('All Categories') }}</h1>

        @if($categories->isNotEmpty())
            <div class="space-y-8">
                @foreach($categories as $category)
                    <section aria-labelledby="category-{{ $category->id }}">
                        <div class="flex items-center justify-between mb-4">
                            <h2 id="category-{{ $category->id }}" class="text-xl font-bold text-gray-900">{{ $category->getDisplayNameAttribute() }}</h2>
                            <a href="{{ route('shop', ['category' => $category->slug]) }}" class="text-primary hover:underline text-sm font-medium">{{ __('View All') }}</a>
                        </div>

                        @if($category->activeChildren->isNotEmpty())
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($category->activeChildren as $child)
                                    <a href="{{ route('shop', ['category' => $child->slug]) }}" class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                                        <div class="aspect-square relative overflow-hidden bg-gray-100">
                                            @if($child->image_path)
                                                <img src="{{ asset('storage/' . $child->image_path) }}"
                                                     alt="{{ $child->getDisplayNameAttribute() }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                    </svg>
                                                </div>
                                            @endif>
                                        </div>
                                        <div class="p-4 text-center">
                                            <h3 class="font-medium text-gray-900">{{ $child->getDisplayNameAttribute() }}</h3>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <a href="{{ route('shop', ['category' => $category->slug]) }}" class="group block bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                                <div class="aspect-video relative overflow-hidden bg-gray-100">
                                    @if($category->image_path)
                                        <img src="{{ asset('storage/' . $category->image_path) }}"
                                             alt="{{ $category->getDisplayNameAttribute() }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                            </svg>
                                        </div>
                                    @endif>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                        <span class="text-white font-medium px-6 py-3 bg-primary/90 rounded-lg">{{ __('Shop Now') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </section>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <h2 class="text-xl font-medium text-gray-900 mb-2">{{ __('No categories available') }}</h2>
                <p class="text-gray-600">{{ __('Check back soon for new categories.') }}</p>
            </div>
        @endif
    </div>

    @livewireLazy('storefront.cart-drawer')
</div>
@endsection