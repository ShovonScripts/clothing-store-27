<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class MarkCodCollectedAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $collectedBy, ?string $note = null): Order
    {
        if (! $order->isCod()) {
            throw new \InvalidArgumentException('Only COD orders can have cash collected');
        }

        if ($order->status !== OrderStatus::Delivered) {
            throw new \InvalidArgumentException('COD can only be collected after delivery');
        }

        if ($order->payment_status === PaymentStatus::Paid) {
            throw new \InvalidArgumentException('COD is already marked as collected');
        }

        return DB::transaction(function () use ($order, $collectedBy, $note) {
            $order->payment()->update([
                'status' => PaymentStatus::Paid,
                'collected_at' => now(),
                'verified_by' => $collectedBy->id,
                'verified_at' => now(),
            ]);

            $this->statusService->recordPayment(
                $order,
                PaymentStatus::Paid,
                $collectedBy,
                $note ?? 'COD collected',
            );

            return $order->refresh();
        });
    }
}
