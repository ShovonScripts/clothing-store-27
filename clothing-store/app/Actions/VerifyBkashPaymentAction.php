<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class VerifyBkashPaymentAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $verifiedBy, ?string $note = null): Order
    {
        if (! $order->isBkash()) {
            throw new \InvalidArgumentException('Order is not a bKash order');
        }

        if ($order->status !== OrderStatus::Pending) {
            throw new \InvalidArgumentException('Only pending bKash orders can be verified');
        }

        if ($order->payment_status !== PaymentStatus::AwaitingVerification) {
            throw new \InvalidArgumentException('Payment is not awaiting verification');
        }

        return DB::transaction(function () use ($order, $verifiedBy, $note) {
            $order->payment()->update([
                'status' => PaymentStatus::Paid,
                'verified_by' => $verifiedBy->id,
                'verified_at' => now(),
            ]);

            $this->statusService->recordPayment(
                $order,
                PaymentStatus::Paid,
                $verifiedBy,
                $note ?? 'bKash payment verified',
            );

            return $this->statusService->transition(
                $order,
                OrderStatus::Confirmed,
                $verifiedBy,
                $note ?? 'bKash payment verified',
            );
        });
    }
}
