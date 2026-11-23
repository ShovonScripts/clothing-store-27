<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'email',
        'is_blacklisted',
        'blacklist_reason',
        'pending_orders_today',
        'last_order_date',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'pending_orders_today' => 'integer',
        'last_order_date' => 'date',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isBlacklisted(): bool
    {
        return $this->is_blacklisted;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->phone;
    }

    public function setPhoneAttribute(string $value): void
    {
        $this->attributes['phone'] = PhoneNormalizer::normalize($value);
    }
}
