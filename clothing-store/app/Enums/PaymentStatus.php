<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case AwaitingVerification = 'awaiting_verification';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => __('Unpaid'),
            self::AwaitingVerification => __('Awaiting Verification'),
            self::Paid => __('Paid'),
            self::Rejected => __('Rejected'),
            self::Refunded => __('Refunded'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'warning',
            self::AwaitingVerification => 'info',
            self::Paid => 'success',
            self::Rejected => 'danger',
            self::Refunded => 'gray',
        };
    }
}
