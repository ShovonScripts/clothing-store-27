<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class ApproveOrderAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $approvedBy, ?string $note = null): Order
    {
        if ($order->status !== OrderStatus::Pending) {
            throw new \InvalidArgumentException('Only pending orders can be approved');
        }

        if ($order->isBkash()) {
            throw new \InvalidArgumentException('bKash orders must be verified through the payment verification flow');
        }

        return DB::transaction(function () use ($order, $approvedBy, $note) {
            return $this->statusService->transition(
                $order,
                OrderStatus::Confirmed,
                $approvedBy,
                $note ?? 'Order approved',
            );
        });
    }
}
