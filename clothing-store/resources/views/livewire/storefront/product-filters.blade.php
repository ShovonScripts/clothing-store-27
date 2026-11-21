<div class="space-y-6" wire:ignore>
    <div class="border-b border-gray-200 pb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Filters') }}</h2>
            @if($filters->hasActiveFilters())
                <button wire:click="clearFilters" class="text-sm text-primary hover:underline">{{ __('Clear all') }}</button>
            @endif
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">{{ __('Category') }}</label>
                <select wire:model="categorySlug" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($filters->categories as $category)
                        <option value="{{ $category->slug }}">{{ $category->display_name }}</option>
                        @foreach($category->activeChildren as $child)
                            <option value="{{ $child->slug }}">&nbsp;&nbsp;{{ $child->display_name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">{{ __('Colors') }}</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($filters->colors as $color)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="colors" value="{{ $color->id }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="w-5 h-5 rounded-full border" style="background-color: {{ $color->hex }}"></span>
                            <span class="text-sm">{{ $color->display_name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">{{ __('Sizes') }}</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'] as $size)
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="sizes" value="{{ $size }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm">{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">{{ __('Price Range') }}</label>
                <div class="flex items-center gap-2">
                    <input type="number" wire:model.debounce.300ms="priceMin" placeholder="Min" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <span class="text-gray-400">-</span>
                    <input type="number" wire:model.debounce.300ms="priceMax" placeholder="Max" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div class="mt-2">
                    <input type="range" wire:model.debounce.300ms="priceMin" min="{{ $filters->priceRange['min'] }}" max="{{ $filters->priceRange['max'] }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary">
                    <input type="range" wire:model.debounce.300ms="priceMax" min="{{ $filters->priceRange['min'] }}" max="{{ $filters->priceRange['max'] }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">{{ __('Sort By') }}</label>
                <select wire:model="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="newest">{{ __('Newest') }}</option>
                    <option value="oldest">{{ __('Oldest') }}</option>
                    <option value="price_asc">{{ __('Price: Low to High') }}</option>
                    <option value="price_desc">{{ __('Price: High to Low') }}</option>
                    <option value="popular">{{ __('Most Popular') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="hidden lg:block sticky top-24">
        <div class="bg-gray-50 rounded-xl p-6 text-center">
            <p class="text-sm text-gray-600 mb-3">{{ __('Need help?') }}</p>
            <a href="https://wa.me/{{ settings('whatsapp_number') }}?text={{ urlencode(__('Hi, I need help with my order.')) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.454.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.471.099-.174.05-.372-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.42.01-.643.078-.223.068-.482.14-.74.213-.257.074-.55.16-.825.273-.274.113-.559.22-.84.324-.282.103-.559.189-.842.273-.283.085-.558.182-.837.249-.278.068-.553.1-.813.114-.274.005-.573-.062-.846-.2-.273-.14-.547-.382-.826-.655-.278-.274-.527-.598-.713-.845-.188-.25-.382-.51-.574-.669-.19-.15-.402-.273-.603-.373-.198-.098-.413-.173-.618-.223zM12 2C6.477 2 2 6.477 2 12c0 1.825.425 3.537 1.168 5.084L2 22l5.452-1.248A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                {{ __('Chat on WhatsApp') }}
            </a>
        </div>
    </div>
</div>