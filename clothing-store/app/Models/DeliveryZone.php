<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'key',
        'name',
        'charge',
        'free_over',
        'est_days_min',
        'est_days_max',
        'is_active',
    ];

    protected $casts = [
        'charge' => 'integer',
        'free_over' => 'integer',
        'est_days_min' => 'integer',
        'est_days_max' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
