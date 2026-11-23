<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'sequence',
    ];

    protected $casts = [
        'date' => 'date',
        'sequence' => 'integer',
    ];
}
