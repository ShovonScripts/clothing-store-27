<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Shop extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $categorySlug = '';

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

    public int $perPage = 12;

    protected Collection $categories;

    protected Collection $colorsList;

    protected array $priceRange;

    public function mount(string $categorySlug = ''): void
    {
        $this->categorySlug = $categorySlug;

        $this->categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('activeChildren')
            ->orderBy('sort_order')
            ->get();

        $this->colorsList = Color::where('is_active', true)->orderBy('sort_order')->get();

        $min = Product::where('is_active', true)->min('price') ?? 0;
        $max = Product::where('is_active', true)->max('price') ?? 10000;
        $this->priceRange = [
            'min' => (int) floor($min / 100) * 100,
            'max' => (int) ceil($max / 100) * 100,
        ];
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

    public function getProductsProperty(): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['category', 'variants.color', 'media'])
            ->when($this->categorySlug, function (Builder $query) {
                $category = Category::where('slug', $this->categorySlug)->first();
                if ($category) {
                    $descendantIds = $category->descendants()->pluck('id')->toArray();
                    $descendantIds[] = $category->id;
                    $query->whereIn('category_id', $descendantIds);
                }
            })
            ->when($this->sizes, function (Builder $query) {
                $query->whereHas('variants', function (Builder $q) {
                    $q->whereIn('size', $this->sizes)->where('is_active', true);
                });
            })
            ->when($this->colors, function (Builder $query) {
                $query->whereHas('variants', function (Builder $q) {
                    $q->whereIn('color_id', $this->colors)->where('is_active', true);
                });
            })
            ->when($this->priceMin > 0, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('price', '>=', $this->priceMin)
                        ->orWhereHas('variants', function (Builder $vq) {
                            $vq->where('price', '>=', $this->priceMin);
                        });
                });
            })
            ->when($this->priceMax > 0, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('price', '<=', $this->priceMax)
                        ->orWhereHas('variants', function (Builder $vq) {
                            $vq->where('price', '<=', $this->priceMax);
                        });
                });
            })
            ->when($this->sort === 'newest', function (Builder $query) {
                $query->latest('published_at');
            })
            ->when($this->sort === 'oldest', function (Builder $query) {
                $query->oldest('published_at');
            })
            ->when($this->sort === 'price_asc', function (Builder $query) {
                $query->orderBy('price', 'asc');
            })
            ->when($this->sort === 'price_desc', function (Builder $query) {
                $query->orderBy('price', 'desc');
            })
            ->when($this->sort === 'popular', function (Builder $query) {
                $query->orderBy('sold_count', 'desc');
            });

        return $query->paginate($this->perPage)->withQueryString();
    }

    public function getCategoriesProperty(): Collection
    {
        return $this->categories;
    }

    public function getColorsProperty(): Collection
    {
        return $this->colorsList;
    }

    public function getPriceRangeProperty(): array
    {
        return $this->priceRange;
    }

    public function render()
    {
        return view('livewire.storefront.shop', [
            'products' => $this->products,
        ]);
    }
}
