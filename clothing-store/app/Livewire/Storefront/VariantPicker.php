<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

    public int $selectedColorId = 0;

    public string $selectedSize = '';

    public function mount(Product $product): void
    {
        $this->product = $product->loadMissing(['variants.color']);

        $active = $this->activeVariants();

        if ($active->isNotEmpty()) {
            $this->selectedColorId = (int) $active->first()->color_id;
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
        $this->availableSizes = $this->activeVariants()
            ->where('color_id', $this->selectedColorId)
            ->pluck('size')
            ->unique()
            ->values()
            ->toArray();

        if (empty($this->availableSizes)) {
            $this->selectedSize = '';
            $this->selectedVariantId = 0;

            return;
        }

        if ($this->selectedSize === '' || ! in_array($this->selectedSize, $this->availableSizes, true)) {
            $this->selectedSize = $this->availableSizes[0];
        }

        $this->updateSelectedVariant();
    }

    public function updateSelectedVariant(): void
    {
        $variant = $this->product->variants->first(function (ProductVariant $variant): bool {
            return $variant->is_active
                && (int) $variant->color_id === $this->selectedColorId
                && $variant->size === $this->selectedSize
                && $variant->stock > 0;
        });

        $this->selectedVariantId = $variant?->id ?? 0;
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < $this->getMaxStock()) {
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
        return $this->selectedVariant()?->stock ?? 0;
    }

    public function getSelectedVariantStock(): int
    {
        return $this->selectedVariant()?->stock ?? 0;
    }

    public function isLowStock(): bool
    {
        $stock = $this->getSelectedVariantStock();

        return $stock > 0 && $stock <= 5;
    }

    public function isOutOfStock(): bool
    {
        return $this->getSelectedVariantStock() <= 0;
    }

    public function getSelectedVariantPrice(): int
    {
        $variant = $this->selectedVariant();

        return $variant ? (int) ($variant->price ?? $this->product->price) : (int) $this->product->price;
    }

    public function addToCart(): void
    {
        if (! $this->selectedVariantId || $this->quantity < 1) {
            return;
        }

        try {
            app(CartService::class)->add($this->selectedVariantId, $this->quantity);
            $this->dispatch('cart-updated');
            $this->dispatch('cart-count-changed', count: app(CartService::class)->count());
            $this->dispatch('open-cart');
            $this->dispatch('notify', message: __('messages.added_to_cart'), type: 'success');

            // Fire Meta Pixel AddToCart event
            $variant = $this->selectedVariant();
            if ($variant) {
                $eventId = (string) Str::uuid();
                $this->dispatch('meta-pixel:add-to-cart', params: [
                    'content_type' => 'product',
                    'content_ids' => [(string) $this->product->id],
                    'content_name' => $this->product->getDisplayNameAttribute(),
                    'content_category' => $this->product->category?->getDisplayNameAttribute() ?? '',
                    'value' => (($variant->price ?? $this->product->price) * $this->quantity) / 100,
                    'currency' => 'BDT',
                ], eventId: $eventId);
            }
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function availableColorOptions(): Collection
    {
        return $this->activeVariants()
            ->pluck('color')
            ->filter()
            ->unique('id')
            ->values();
    }

    public function sizeAvailable(string $size): bool
    {
        return $this->activeVariants()
            ->where('color_id', $this->selectedColorId)
            ->where('size', $size)
            ->isNotEmpty();
    }

    public function sizeLowStock(string $size): bool
    {
        return $this->activeVariants()
            ->where('color_id', $this->selectedColorId)
            ->where('size', $size)
            ->where('stock', '<=', 5)
            ->isNotEmpty();
    }

    public function render()
    {
        return view('livewire.storefront.variant-picker', [
            'product' => $this->product,
            'variantPicker' => $this,
        ]);
    }

    private function activeVariants(): Collection
    {
        return $this->product->variants
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->values();
    }

    private function selectedVariant(): ?ProductVariant
    {
        if (! $this->selectedVariantId) {
            return null;
        }

        return $this->product->variants->firstWhere('id', $this->selectedVariantId);
    }
}
