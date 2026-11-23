<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_orders');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('view_order_details');
    }

    public function approve(User $user): bool
    {
        return $user->can('approve_orders');
    }

    public function verifyBkash(User $user): bool
    {
        return $user->can('payments.verify') || $user->can('verify_bkash_payments');
    }

    public function changeStatus(User $user): bool
    {
        return $user->can('change_order_status');
    }

    public function manageCourier(User $user): bool
    {
        return $user->can('manage_order_courier');
    }

    public function markCodCollected(User $user): bool
    {
        return $user->can('mark_cod_collected');
    }

    public function print(User $user): bool
    {
        return $user->can('print_invoices');
    }

    public function cancel(User $user): bool
    {
        return $user->can('cancel_orders');
    }
}
