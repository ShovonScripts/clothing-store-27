<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = [
        'name',
        'name_bn',
        'zone',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->orderBy('sort_order');
    }

    public function activeAreas(): HasMany
    {
        return $this->areas()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInsideDhaka($query)
    {
        return $query->where('zone', 'inside_dhaka');
    }

    public function scopeOutsideDhaka($query)
    {
        return $query->where('zone', 'outside_dhaka');
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'bn' && $this->name_bn
            ? $this->name_bn
            : $this->name;
    }
}
