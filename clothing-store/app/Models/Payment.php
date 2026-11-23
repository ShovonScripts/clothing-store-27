<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'status',
        'bkash_trxid',
        'bkash_sender',
        'bkash_amount_sent',
        'bkash_receiver_number',
        'amount',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'collected_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'integer',
        'bkash_amount_sent' => 'integer',
        'verified_at' => 'datetime',
        'collected_at' => 'datetime',
        'meta' => 'array',
        'method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isAwaitingVerification(): bool
    {
        return $this->status === PaymentStatus::AwaitingVerification;
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }
}
