<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class ShipOrderAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $by, string $courier, string $consignmentId, ?string $trackingUrl = null): Order
    {
        if (trim($courier) === '' || trim($consignmentId) === '') {
            throw new \InvalidArgumentException('Courier and consignment ID are required');
        }

        return DB::transaction(function () use ($order, $by, $courier, $consignmentId, $trackingUrl) {
            $order->update([
                'courier' => $courier,
                'consignment_id' => $consignmentId,
                'tracking_url' => $trackingUrl,
            ]);

            return $this->statusService->transition(
                $order,
                OrderStatus::Shipped,
                $by,
                sprintf('Shipped via %s (%s)', $courier, $consignmentId),
            );
        });
    }
}
