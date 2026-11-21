<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color_id',
        'price',
        'stock',
        'low_stock_threshold',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function getEffectivePrice(): int
    {
        return $this->price ?? $this->product->price;
    }

    public function isLowStock(): bool
    {
        $threshold = $this->low_stock_threshold ?? $this->product->category->products->first()?->category?->products->first()?->category?->products->first()?->category->settings()?->low_stock_threshold ?? 5;

        return $this->stock <= $threshold && $this->stock > 0;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function getVariantLabel(): string
    {
        $colorName = $this->color?->getDisplayNameAttribute() ?? '';

        return trim("{$this->size} {$colorName}");
    }
}
