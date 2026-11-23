<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $cancelledBy, string $reason): Order
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('A cancellation reason is required');
        }

        return DB::transaction(function () use ($order, $cancelledBy, $reason) {
            return $this->statusService->transition(
                $order,
                OrderStatus::Cancelled,
                $cancelledBy,
                $reason,
                restock: true,
            );
        });
    }
}
