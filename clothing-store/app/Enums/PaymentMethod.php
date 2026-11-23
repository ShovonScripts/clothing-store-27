<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case BkashSendMoney = 'bkash_send_money';

    public function label(): string
    {
        return match ($this) {
            self::Cod => __('Cash on Delivery'),
            self::BkashSendMoney => __('bKash Send Money'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Cod => __('Pay when your order is delivered'),
            self::BkashSendMoney => __('Send money via bKash and enter TrxID'),
        };
    }

    public function requiresVerification(): bool
    {
        return $this === self::BkashSendMoney;
    }

    public function getRawLabel(): string
    {
        return match ($this) {
            self::Cod => 'Cash on Delivery',
            self::BkashSendMoney => 'bKash Send Money',
        };
    }
}
