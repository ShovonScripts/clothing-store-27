<header class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16 lg:h-14">
            <div class="flex items-center gap-4">
                <button 
                    @click="$dispatch('toggle-mobile-menu')" 
                    class="lg:hidden p-2 text-gray-600 hover:text-gray-900"
                    aria-label="Toggle menu"
                    aria-expanded="false"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="{{ config('store.name', '{BRAND}') }} - Home">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-primary hidden sm:block">{{ config('store.name', '{BRAND}') }}</span>
                </a>
            </div>

            <nav class="hidden lg:flex items-center gap-6" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">{{ __('Home') }}</a>
                <a href="{{ route('shop') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">{{ __('Shop') }}</a>
                <a href="{{ route('categories') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">{{ __('Categories') }}</a>
                <a href="{{ route('pages.show', 'size-guide') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">{{ __('Size Guide') }}</a>
                <a href="{{ route('pages.show', 'delivery-info') }}" class="text-sm font-medium text-gray-700 hover:text-primary transition-colors">{{ __('Delivery') }}</a>
            </nav>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2">
                    <button 
                        @click="switchLocale('bn')" 
                        :class="['px-2 py-1 text-xs font-medium rounded', locale === 'bn' ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900']"
                    >
                        বাংলা
                    </button>
                    <button 
                        @click="switchLocale('en')" 
                        :class="['px-2 py-1 text-xs font-medium rounded', locale === 'en' ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900']"
                    >
                        EN
                    </button>
                </div>

                <button @click="$dispatch('open-cart')" class="relative p-2 text-gray-600 hover:text-gray-900" aria-label="{{ __('Cart') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                    </svg>
                </button>

                <div class="sm:hidden flex items-center gap-2">
                    <button 
                        @click="switchLocale('bn')" 
                        :class="['px-2 py-1 text-xs font-medium rounded', locale === 'bn' ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900']"
                    >
                        বাংলা
                    </button>
                    <button 
                        @click="switchLocale('en')" 
                        :class="['px-2 py-1 text-xs font-medium rounded', locale === 'en' ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900']"
                    >
                        EN
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="lg:hidden border-t border-gray-100 py-4" @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen" x-data="{ mobileMenuOpen: false }">
            <nav class="flex flex-col gap-2" aria-label="Mobile navigation">
                <a href="{{ route('home') }}" class="px-2 py-2 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded">{{ __('Home') }}</a>
                <a href="{{ route('shop') }}" class="px-2 py-2 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded">{{ __('Shop') }}</a>
                <a href="{{ route('categories') }}" class="px-2 py-2 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded">{{ __('Categories') }}</a>
                <a href="{{ route('pages.show', 'size-guide') }}" class="px-2 py-2 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded">{{ __('Size Guide') }}</a>
                <a href="{{ route('pages.show', 'delivery-info') }}" class="px-2 py-2 text-sm font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded">{{ __('Delivery') }}</a>
            </nav>
        </div>
    </div>
</header>