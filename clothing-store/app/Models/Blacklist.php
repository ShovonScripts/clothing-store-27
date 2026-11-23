<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blacklist extends Model
{
    public const TYPE_PHONE = 'phone';

    public const TYPE_IP = 'ip';

    public const TYPE_ADDRESS_KEYWORD = 'address_keyword';

    protected $fillable = [
        'type',
        'value',
        'reason',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
