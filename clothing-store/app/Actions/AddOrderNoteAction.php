<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Order;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

class AddOrderNoteAction
{
    public function __construct(protected ActivityLogger $logger) {}

    public function execute(Order $order, User $by, string $note): void
    {
        if (trim($note) === '') {
            throw new \InvalidArgumentException('Note cannot be empty');
        }

        DB::transaction(function () use ($order, $by, $note) {
            $this->logger->note($order, $by, $note);
        });
    }
}
