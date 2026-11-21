<div class="relative">
    <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden relative">
        @if($currentImage)
            <img src="{{ $currentImage['url'] }}"
                 alt="{{ $currentImage['alt'] }}"
                 class="w-full h-full object-cover transition-opacity duration-300"
                 loading="eager"
                 fetchpriority="high">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
            </div>
        @endif

        @if($hasMultipleImages)
            <button type="button"
                    wire:click="previousImage"
                    class="absolute left-2 top-1/2 -translate-y-1/2 p-2 bg-white/80 rounded-full hover:bg-white shadow-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    aria-label="{{ __('Previous image') }}">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <button type="button"
                    wire:click="nextImage"
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-white/80 rounded-full hover:bg-white shadow-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    aria-label="{{ __('Next image') }}">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1" role="tablist" aria-label="{{ __('Image thumbnails') }}">
                @foreach($images as $index => $image)
                    <button type="button"
                            wire:click="setCurrentImage({{ $index }})"
                            class="w-2 h-2 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-primary
                                {{ $index == $currentImageIndex ? 'bg-primary' : 'bg-white/70 hover:bg-white' }}"
                            role="tab"
                            aria-selected="{{ $index == $currentImageIndex ? 'true' : 'false' }}"
                            aria-label="{{ __('Image :number', ['number' => $index + 1]) }}"></button>
                @endforeach
            </div>
        @endif
    </div>

    @if($hasMultipleImages)
        <div class="mt-3 overflow-x-auto pb-2" wire:ignore>
            <div class="flex gap-2 min-w-max" x-data="{ scrollLeft: 0 }">
                @foreach($images as $index => $image)
                    <button type="button"
                            wire:click="setCurrentImage({{ $index }})"
                            class="relative w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all
                                {{ $index == $currentImageIndex ? 'border-primary' : 'border-transparent hover:border-gray-300' }}"
                            aria-label="{{ __('View image :number', ['number' => $index + 1]) }}"
                            aria-current="{{ $index == $currentImageIndex ? 'true' : 'false' }}">
                        <img src="{{ $image['thumb'] }}" alt="{{ $image['alt'] }}" class="w-full h-full object-cover">
                        @if($index == $currentImageIndex)
                            <div class="absolute inset-0 bg-primary/20"></div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>