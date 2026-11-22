<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductFilters extends Component
{
    #[Url(history: true)]
    public array $sizes = [];

    #[Url(history: true)]
    public array $colors = [];

    #[Url(history: true)]
    public int $priceMin = 0;

    #[Url(history: true)]
    public int $priceMax = 0;

    #[Url(history: true)]
    public string $sort = 'newest';

    #[Url(history: true)]
    public string $categorySlug = '';

    public function mount(string $categorySlug = ''): void
    {
        $this->categorySlug = $categorySlug;
    }

    public function updatingSizes(): void
    {
        $this->resetPage();
    }

    public function updatingColors(): void
    {
        $this->resetPage();
    }

    public function updatingPriceMin(): void
    {
        $this->resetPage();
    }

    public function updatingPriceMax(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function updatedCategorySlug(string $value): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->sizes = [];
        $this->colors = [];
        $this->priceMin = 0;
        $this->priceMax = 0;
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return ! empty($this->sizes) || ! empty($this->colors) || $this->priceMin > 0 || $this->priceMax > 0;
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('activeChildren')
            ->orderBy('sort_order')
            ->get();
    }

    public function getColorsProperty(): Collection
    {
        return Color::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function getPriceRangeProperty(): array
    {
        $min = Product::where('is_active', true)->min('price') ?? 0;
        $max = Product::where('is_active', true)->max('price') ?? 10000;

        return [
            'min' => (int) floor($min / 100) * 100,
            'max' => (int) ceil($max / 100) * 100,
        ];
    }

    public function render()
    {
        return view('livewire.storefront.product-filters');
    }
}
