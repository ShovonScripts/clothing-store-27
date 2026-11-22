<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case Bkash = 'bkash';

    public function label(): string
    {
        return match ($this) {
            self::Cod => __('Cash on Delivery'),
            self::Bkash => __('bKash Send Money'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Cod => __('Pay when your order is delivered'),
            self::Bkash => __('Send money via bKash and enter TrxID'),
        };
    }

    public function requiresVerification(): bool
    {
        return $this === self::Bkash;
    }

    public function getRawLabel(): string
    {
        return match ($this) {
            self::Cod => 'Cash on Delivery',
            self::Bkash => 'bKash Send Money',
        };
    }
}
