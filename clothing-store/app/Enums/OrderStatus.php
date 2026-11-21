<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case FailedDelivery = 'failed_delivery';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Confirmed => __('Confirmed'),
            self::Processing => __('Processing'),
            self::Shipped => __('Shipped'),
            self::Delivered => __('Delivered'),
            self::Cancelled => __('Cancelled'),
            self::FailedDelivery => __('Failed Delivery'),
            self::Returned => __('Returned'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Processing => 'primary',
            self::Shipped => 'blue',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
            self::FailedDelivery => 'danger',
            self::Returned => 'gray',
        };
    }

    public static function allowedTransitions(): array
    {
        return [
            self::Pending->value => [self::Confirmed, self::Cancelled],
            self::Confirmed->value => [self::Processing, self::Shipped, self::Cancelled],
            self::Processing->value => [self::Shipped, self::Cancelled],
            self::Shipped->value => [self::Delivered, self::FailedDelivery, self::Returned],
            self::FailedDelivery->value => [self::Shipped, self::Returned, self::Cancelled],
            self::Delivered->value => [self::Returned],
            self::Cancelled->value => [],
            self::Returned->value => [],
        ];
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, self::allowedTransitions()[$this->value] ?? []);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled, self::Returned]);
    }
}
