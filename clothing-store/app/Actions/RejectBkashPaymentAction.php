<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class RejectBkashPaymentAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $rejectedBy, string $reason): Order
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('A rejection reason is required');
        }

        if (! $order->isBkash()) {
            throw new \InvalidArgumentException('Order is not a bKash order');
        }

        if ($order->status !== OrderStatus::Pending) {
            throw new \InvalidArgumentException('Only pending bKash orders can be rejected');
        }

        return DB::transaction(function () use ($order, $rejectedBy, $reason) {
            $order->payment()->update([
                'status' => PaymentStatus::Rejected,
                'rejection_reason' => $reason,
                'verified_by' => $rejectedBy->id,
                'verified_at' => now(),
            ]);

            $this->statusService->recordPayment(
                $order,
                PaymentStatus::Rejected,
                $rejectedBy,
                $reason,
            );

            return $this->statusService->transition(
                $order,
                OrderStatus::Cancelled,
                $rejectedBy,
                $reason,
                restock: true,
            );
        });
    }
}
