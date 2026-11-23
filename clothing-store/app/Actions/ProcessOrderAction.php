<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class ProcessOrderAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $by, ?string $note = null): Order
    {
        return DB::transaction(fn () => $this->statusService->transition(
            $order,
            OrderStatus::Processing,
            $by,
            $note ?? 'Order moved to processing',
        ));
    }
}
