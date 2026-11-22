<div wire:show="isOpen" wire:transition.enter="transition ease-out duration-300" wire:transition.enter-start="opacity-0 transform translate-x-full" wire:transition.enter-end="opacity-100 transform translate-x-0" wire:transition.leave="transition ease-in duration-200" wire:transition.leave-start="opacity-100 transform translate-x-0" wire:transition.leave-end="opacity-0 transform translate-x-full" class="fixed inset-0 z-50 lg:hidden" @click.outside="close()" @cart-drawer-opened.window="isOpen = true">
    <div class="fixed inset-0 bg-black/50" @click="close()"></div>

    <div class="fixed right-0 top-0 h-full w-full max-w-sm bg-white shadow-xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Cart') }}</h2>
            <button type="button" wire:click="close" class="p-2 text-gray-500 hover:text-gray-700" aria-label="{{ __('Close cart') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <div class="text-center py-12">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" />
                </svg>
                <p class="text-gray-600">{{ __('Your cart is empty') }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ __('Cart functionality will be available in Phase 3.') }}</p>
            </div>
        </div>
    </div>
</div>