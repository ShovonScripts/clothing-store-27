<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class NewOrderNotification extends Notification
{
    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $routeName = 'filament.admin.resources.orders.view';

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => __('New order received'),
            'body' => __('Order :number for :total', [
                'number' => $this->order->order_number,
                'total' => moneyShort((int) $this->order->total),
            ]),
            'url' => Route::has($routeName)
                ? route($routeName, ['record' => $this->order])
                : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
