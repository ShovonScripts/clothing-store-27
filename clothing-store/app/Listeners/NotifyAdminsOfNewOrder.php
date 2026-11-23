<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;
use App\Notifications\NewOrderNotification;

class NotifyAdminsOfNewOrder
{
    public function handle(OrderPlaced $event): void
    {
        User::query()
            ->where('is_active', true)
            ->permission('view_orders')
            ->distinct()
            ->each(fn (User $user) => $user->notify(new NewOrderNotification($event->order)));
    }
}
