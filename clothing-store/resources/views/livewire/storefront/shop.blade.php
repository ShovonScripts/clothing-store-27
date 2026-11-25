<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="lg:w-64 flex-shrink-0 hidden lg:block">
                @livewire('storefront.product-filters', ['categorySlug' => $categorySlug], key('product-filters'))
            </aside>

            <main class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('All Products') }}</h1>
                        <p class="text-gray-600 mt-1">{{ $products->total() }} {{ __('products found') }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <select wire:model="perPage" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="12">{{ __('12 per page') }}</option>
                            <option value="24">{{ __('24 per page') }}</option>
                            <option value="48">{{ __('48 per page') }}</option>
                        </select>

                        <div class="hidden sm:flex items-center gap-2 border border-gray-300 rounded-lg overflow-hidden">
                            <button type="button" class="px-4 py-2 bg-white border-r border-gray-300 hover:bg-gray-50" wire:click="$dispatch('set-view', 'grid')" aria-label="{{ __('Grid view') }}">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                            <button type="button" class="px-4 py-2 bg-white hover:bg-gray-50" wire:click="$dispatch('set-view', 'list')" aria-label="{{ __('List view') }}">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h2 class="text-xl font-medium text-gray-900 mb-2">{{ __('No products found') }}</h2>
                        <p class="text-gray-600 mb-6">{{ __('Try adjusting your filters or browse all categories.') }}</p>
                        <button wire:click="clearFilters" class="text-primary hover:underline font-medium">{{ __('Clear all filters') }}</button>
                    </div>
                @endif
            </main>
        </div>
    </div>

    @livewireLazy('storefront.cart-drawer')
</div>