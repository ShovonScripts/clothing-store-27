<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class OrderNumberService
{
    /**
     * Generate the next order number for today: YYMMDD + 4-digit daily sequence.
     */
    public function next(): string
    {
        $date = now()->toDateString();
        $sequence = app(DailyCounterService::class)->next($date);

        return now()->format('ymd').str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function forOrder(Order $order): string
    {
        return $order->order_number;
    }
}
