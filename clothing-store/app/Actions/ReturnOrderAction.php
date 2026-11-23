<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class ReturnOrderAction
{
    public function __construct(protected OrderStatusService $statusService) {}

    public function execute(Order $order, User $by, bool $restock = false, ?string $note = null): Order
    {
        return DB::transaction(fn () => $this->statusService->transition(
            $order,
            OrderStatus::Returned,
            $by,
            $note ?? ($restock ? 'Order returned and restocked' : 'Order returned'),
            restock: $restock,
        ));
    }
}
