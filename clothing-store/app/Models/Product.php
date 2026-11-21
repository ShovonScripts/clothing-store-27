<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'name',
        'name_bn',
        'slug',
        'sku',
        'short_description',
        'description',
        'fabric',
        'fit',
        'care',
        'price',
        'compare_at_price',
        'cost_price',
        'is_active',
        'is_featured',
        'is_new',
        'size_chart_id',
        'meta_title',
        'meta_description',
        'sold_count',
        'views',
        'published_at',
    ];

    protected $casts = [
        'price' => 'integer',
        'compare_at_price' => 'integer',
        'cost_price' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'sold_count' => 'integer',
        'views' => 'integer',
        'published_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(400)
                    ->height(400)
                    ->format('webp')
                    ->quality(80);
                $this->addMediaConversion('card')
                    ->width(600)
                    ->height(600)
                    ->format('webp')
                    ->quality(80);
                $this->addMediaConversion('large')
                    ->width(1200)
                    ->height(1200)
                    ->format('webp')
                    ->quality(85);
            });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizeChart(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function getEffectivePrice(int $variantId = 0): int
    {
        if ($variantId) {
            $variant = $this->variants()->find($variantId);
            if ($variant && $variant->price !== null) {
                return $variant->price;
            }
        }

        return $this->price;
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->name_bn
            ? $this->name_bn
            : $this->name;
    }

    public function getFirstImageUrl(string $conversion = 'card'): ?string
    {
        $media = $this->getFirstMedia('gallery');

        return $media?->getUrl($conversion);
    }
}
