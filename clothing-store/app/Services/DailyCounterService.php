<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DailyCounter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DailyCounterService
{
    /**
     * Atomically increment and return the sequence for a given date.
     */
    public function next(string $date): int
    {
        return DB::transaction(function () use ($date) {
            $counter = DailyCounter::where('date', $date)->lockForUpdate()->first();

            if (! $counter) {
                try {
                    $counter = DailyCounter::create(['date' => $date, 'sequence' => 0]);
                } catch (QueryException $e) {
                    // Another request created the row concurrently; retry.
                    $counter = DailyCounter::where('date', $date)->lockForUpdate()->firstOrFail();
                }
            }

            $counter->increment('sequence');
            $counter->refresh();

            return (int) $counter->sequence;
        });
    }
}
