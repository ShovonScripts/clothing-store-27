<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminsOfNewOrder implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderPlaced $event): void
    {
        Log::info('NotifyAdminsOfNewOrder listener called', ['order_id' => $event->order->id]);

        $admins = User::query()
            ->where('is_active', true)
            ->permission('view_orders')
            ->distinct()
            ->get();

        Log::info('Admins found with view_orders permission', ['count' => $admins->count()]);

        foreach ($admins as $user) {
            Log::info('Sending notification to user', ['user_id' => $user->id, 'user_name' => $user->name]);
            $user->notify(new NewOrderNotification($event->order));
        }
    }
}
