<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'allowed_payment_methods',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'min_order_amount' => 'integer',
        'max_discount' => 'integer',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'per_user_limit' => 'integer',
        'allowed_payment_methods' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidFor(int $subtotal, string|PaymentMethod $paymentMethod = PaymentMethod::Cod, ?string $phone = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < $this->min_order_amount) {
            return false;
        }

        if ($this->allowed_payment_methods) {
            $method = $paymentMethod instanceof PaymentMethod ? $paymentMethod->value : $paymentMethod;
            $allowed = array_map('strval', $this->allowed_payment_methods);
            if (! in_array((string) $method, $allowed, true)) {
                return false;
            }
        }

        if ($this->per_user_limit && $phone) {
            $usage = $this->usages()->where('phone', $phone)->count();
            if ($usage >= $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(int $subtotal): int
    {
        $discount = match ($this->type) {
            'percentage' => (int) round($subtotal * $this->value / 100),
            'fixed' => $this->value,
            default => 0,
        };

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $subtotal);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
