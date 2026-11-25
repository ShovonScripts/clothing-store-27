<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductGallery extends Component
{
    #[Url(history: true)]
    public int $currentImageIndex = 0;

    public Product $product;

    public array $images = [];

    public function mount(Product $product): void
    {
        $this->product = $product->loadMissing('media');
        $this->loadImages();
    }

    public function loadImages(): void
    {
        $media = $this->product->getMedia('gallery');
        $this->images = $media->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl('large'),
                'thumb' => $media->getUrl('thumb'),
                'alt' => $media->getCustomProperty('alt') ?? $this->product->getDisplayNameAttribute(),
            ];
        })->toArray();
    }

    public function setCurrentImage(int $index): void
    {
        if ($index >= 0 && $index < count($this->images)) {
            $this->currentImageIndex = $index;
        }
    }

    public function nextImage(): void
    {
        $this->currentImageIndex = ($this->currentImageIndex + 1) % count($this->images);
    }

    public function previousImage(): void
    {
        $this->currentImageIndex = ($this->currentImageIndex - 1 + count($this->images)) % count($this->images);
    }

    public function getCurrentImage(): ?array
    {
        return $this->images[$this->currentImageIndex] ?? null;
    }

    public function getImageCount(): int
    {
        return count($this->images);
    }

    public function hasMultipleImages(): bool
    {
        return count($this->images) > 1;
    }

    public function render()
    {
        return view('livewire.storefront.product-gallery', [
            'product' => $this->product,
            'currentImage' => $this->getCurrentImage(),
            'hasMultipleImages' => $this->hasMultipleImages(),
            'currentImageIndex' => $this->currentImageIndex,
            'images' => $this->images,
        ]);
    }
}
