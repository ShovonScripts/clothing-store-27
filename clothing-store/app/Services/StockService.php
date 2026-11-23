<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\OutOfStockException;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function reserve(array $items): bool
    {
        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $variant = ProductVariant::where('id', $item['variant_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($variant->stock < $item['qty']) {
                    throw new OutOfStockException($variant, $item['qty']);
                }

                $variant->decrement('stock', $item['qty']);
            }
        });

        return true;
    }

    public function release(array $items): void
    {
        foreach ($items as $item) {
            ProductVariant::where('id', $item['variant_id'])->increment('stock', $item['qty']);
        }
    }

    public function checkAvailability(array $items): array
    {
        $results = [];
        foreach ($items as $item) {
            $variant = ProductVariant::where('id', $item['variant_id'])
                ->where('is_active', true)
                ->first();

            $results[$item['variant_id']] = [
                'available' => $variant?->stock ?? 0,
                'requested' => $item['qty'],
                'sufficient' => ($variant?->stock ?? 0) >= $item['qty'],
            ];
        }

        return $results;
    }
}
