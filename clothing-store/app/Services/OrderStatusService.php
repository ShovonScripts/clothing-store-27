<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    /**
     * Validate and apply an order status transition.
     *
     * @throws InvalidOrderTransitionException
     */
    public function transition(
        Order $order,
        OrderStatus $to,
        ?User $by = null,
        ?string $note = null,
        bool $restock = false,
    ): Order {
        if (! $order->status->canTransitionTo($to)) {
            throw new InvalidOrderTransitionException($order->status, $to);
        }

        return DB::transaction(function () use ($order, $to, $by, $note, $restock) {
            $from = $order->status;
            $paymentBefore = $order->payment_status;

            $attributes = ['status' => $to];

            $attributes = array_merge($attributes, match ($to) {
                OrderStatus::Confirmed => [
                    'approved_at' => now(),
                    'approved_by' => $by?->id,
                ],
                OrderStatus::Shipped => ['shipped_at' => now()],
                OrderStatus::Delivered => ['delivered_at' => now()],
                OrderStatus::Cancelled => [
                    'cancelled_at' => now(),
                    'cancelled_by' => $by?->id,
                    'cancel_reason' => $note,
                ],
                default => [],
            });

            $order->update($attributes);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $from,
                'to_status' => $to,
                'from_payment_status' => $paymentBefore,
                'to_payment_status' => $order->payment_status,
                'type' => 'order',
                'changed_by' => $by?->id,
                'note' => $note,
            ]);

            if ($restock) {
                app(StockService::class)->releaseForOrder($order);
            }

            app(ActivityLogger::class)->orderTransition($order, $from, $to, $by, $note);

            return $order->refresh();
        });
    }

    /**
     * Record a payment status change without changing the order status.
     */
    public function recordPayment(
        Order $order,
        PaymentStatus $to,
        ?User $by = null,
        ?string $note = null,
    ): void {
        $from = $order->payment_status;

        if ($from === $to) {
            return;
        }

        $order->update(['payment_status' => $to]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $order->status,
            'to_status' => $order->status,
            'from_payment_status' => $from,
            'to_payment_status' => $to,
            'type' => 'payment',
            'changed_by' => $by?->id,
            'note' => $note,
        ]);

        app(ActivityLogger::class)->paymentTransition($order, $from, $to, $by, $note);
    }
}
