<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'phone',
        'name',
        'email',
        'is_blacklisted',
        'blacklist_reason',
        'pending_orders_today',
        'last_order_date',
        'total_orders',
        'delivered_orders',
        'cancelled_orders',
        'total_spent',
        'notes',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'pending_orders_today' => 'integer',
        'last_order_date' => 'date',
        'total_orders' => 'integer',
        'delivered_orders' => 'integer',
        'cancelled_orders' => 'integer',
        'total_spent' => 'integer',
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

    public function getRiskBadgeAttribute(): ?string
    {
        if ($this->is_blacklisted) {
            return 'blacklisted';
        }

        if ($this->cancelled_orders >= 2) {
            return 'high_risk';
        }

        return null;
    }

    public function setPhoneAttribute(string $value): void
    {
        $this->attributes['phone'] = PhoneNormalizer::normalize($value) ?? $value;
    }
}
