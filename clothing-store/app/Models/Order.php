<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'subtotal',
        'delivery_charge',
        'discount',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'bkash_trxid',
        'bkash_sender',
        'bkash_amount',
        'shipping_name',
        'shipping_phone',
        'shipping_district_id',
        'shipping_area_id',
        'shipping_address',
        'note',
        'coupon_id',
        'discount_amount',
        'items_snapshot',
        'approved_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'delivery_charge' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'discount_amount' => 'integer',
        'bkash_amount' => 'integer',
        'items_snapshot' => 'array',
        'approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'shipping_district_id');
    }

    public function shippingArea(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'shipping_area_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->payment_status === 'verified';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }
}
