<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SizeChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'headers',
        'rows',
        'is_active',
    ];

    protected $casts = [
        'headers' => 'array',
        'rows' => 'array',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
