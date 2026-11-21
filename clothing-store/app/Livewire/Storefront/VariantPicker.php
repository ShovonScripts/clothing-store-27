<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\Url;
use Livewire\Component;

class VariantPicker extends Component
{
    #[Url(history: true)]
    public int $selectedVariantId = 0;

    #[Url(history: true)]
    public int $quantity = 1;

    public Product $product;

    public array $availableSizes = [];

    public array $availableColors = [];

    public int $selectedColorId = 0;

    public string $selectedSize = '';

    public function mount(Product $product): void
    {
        $this->product = $product->load(['variants.color']);

        $this->availableSizes = $this->product->activeVariants()
            ->where('stock', '>', 0)
            ->distinct('size')
            ->pluck('size')
            ->unique()
            ->values()
            ->toArray();

        $this->availableColors = $this->product->activeVariants()
            ->where('stock', '>', 0)
            ->with('color')
            ->get()
            ->pluck('color')
            ->unique('id')
            ->values()
            ->toArray();

        // Auto-select first available color and size
        if (! empty($this->availableColors)) {
            $this->selectedColorId = $this->availableColors[0]['id'];
            $this->updateAvailableSizes();
        }
    }

    public function updatedSelectedColorId(int $colorId): void
    {
        $this->selectedSize = '';
        $this->updateAvailableSizes();
    }

    public function updatedSelectedSize(string $size): void
    {
        $this->updateSelectedVariant();
    }

    public function updateAvailableSizes(): void
    {
        $this->availableSizes = $this->product->activeVariants()
            ->where('color_id', $this->selectedColorId)
            ->where('stock', '>', 0)
            ->pluck('size')
            ->unique()
            ->values()
            ->toArray();

        // Auto-select first available size
        if (! empty($this->availableSizes) && empty($this->selectedSize)) {
            $this->selectedSize = $this->availableSizes[0];
            $this->updateSelectedVariant();
        }
    }

    public function updateSelectedVariant(): void
    {
        if ($this->selectedColorId && $this->selectedSize) {
            $variant = ProductVariant::where('product_id', $this->product->id)
                ->where('color_id', $this->selectedColorId)
                ->where('size', $this->selectedSize)
                ->where('is_active', true)
                ->first();

            $this->selectedVariantId = $variant?->id ?? 0;
        } else {
            $this->selectedVariantId = 0;
        }
    }

    public function incrementQuantity(): void
    {
        $maxStock = $this->getMaxStock();
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function getMaxStock(): int
    {
        if ($this->selectedVariantId) {
            $variant = ProductVariant::find($this->selectedVariantId);

            return $variant?->stock ?? 0;
        }

        return $this->product->activeVariants()->sum('stock');
    }

    public function getSelectedVariantStock(): int
    {
        if ($this->selectedVariantId) {
            $variant = ProductVariant::find($this->selectedVariantId);

            return $variant?->stock ?? 0;
        }

        return 0;
    }

    public function isLowStock(): bool
    {
        $stock = $this->getSelectedVariantStock();
        $threshold = 5; // Default low stock threshold

        return $stock > 0 && $stock <= $threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->getSelectedVariantStock() <= 0;
    }

    public function getSelectedVariantPrice(): int
    {
        if ($this->selectedVariantId) {
            $variant = ProductVariant::find($this->selectedVariantId);

            return $variant?->getEffectivePrice() ?? 0;
        }

        return $this->product->price;
    }

    public function addToCart(): void
    {
        if ($this->selectedVariantId && $this->quantity > 0) {
            $this->dispatch('add-to-cart', variantId: $this->selectedVariantId, quantity: $this->quantity);
            $this->dispatch('notify', message: __('Added to cart'), type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.storefront.variant-picker', [
            'product' => $this->product,
            'variantPicker' => $this,
        ]);
    }
}
