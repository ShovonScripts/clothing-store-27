<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasSlug;

    protected $fillable = [
        'slug',
        'title',
        'title_bn',
        'body',
        'body_bn',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayTitleAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->title_bn
            ? $this->title_bn
            : $this->title;
    }

    public function getDisplayBodyAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->body_bn
            ? $this->body_bn
            : $this->body;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->getDisplayTitleAttribute();
    }

    public function getDisplayContentAttribute(): string
    {
        return $this->getDisplayBodyAttribute();
    }
}
