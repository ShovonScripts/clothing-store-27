<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;

class ActivityLogger
{
    public function orderTransition(Order $order, OrderStatus $from, OrderStatus $to, ?User $by, ?string $note): void
    {
        $this->log($order, 'orders', sprintf('Order status changed from %s to %s', $from->value, $to->value), $by, [
            'from' => $from->value,
            'to' => $to->value,
            'note' => $note,
        ]);
    }

    public function paymentTransition(Order $order, PaymentStatus $from, PaymentStatus $to, ?User $by, ?string $note): void
    {
        $this->log($order, 'payments', sprintf('Payment status changed from %s to %s', $from->value, $to->value), $by, [
            'from' => $from->value,
            'to' => $to->value,
            'note' => $note,
        ]);
    }

    public function note(Order $order, User $by, string $note): void
    {
        $this->log($order, 'order_notes', 'Admin note added', $by, ['note' => $note]);
    }

    public function log(
        Order $order,
        string $logName,
        string $description,
        ?User $by,
        array $properties = [],
    ): void {
        $builder = activity($logName)
            ->performedOn($order)
            ->withProperties(array_merge($properties, [
                'order_number' => $order->order_number,
            ]));

        if ($by) {
            $builder->causedBy($by);
        }

        $builder->log($description);
    }
}
