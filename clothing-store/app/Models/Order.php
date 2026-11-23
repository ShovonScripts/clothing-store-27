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
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'customer_email',
        'shipping_district_id',
        'shipping_area_id',
        'shipping_address',
        'delivery_zone_key',
        'note',
        'coupon_id',
        'discount_amount',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
        'courier',
        'consignment_id',
        'tracking_url',
        'shipped_at',
        'delivered_at',
        'cod_amount',
        'items_snapshot',
        'ip_address',
        'user_agent',
        'source',
        'admin_note',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'delivery_charge' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'discount_amount' => 'integer',
        'bkash_amount' => 'integer',
        'cod_amount' => 'integer',
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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isVerified(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Processing], true);
    }

    public function isBkash(): bool
    {
        return $this->payment_method === PaymentMethod::BkashSendMoney;
    }

    public function isCod(): bool
    {
        return $this->payment_method === PaymentMethod::Cod;
    }
}
