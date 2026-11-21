<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'key' => 'inside_dhaka',
                'name' => 'Inside Dhaka',
                'charge' => 70,
                'free_over' => 0,
                'est_days_min' => 1,
                'est_days_max' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'outside_dhaka',
                'name' => 'Outside Dhaka',
                'charge' => 130,
                'free_over' => 0,
                'est_days_min' => 2,
                'est_days_max' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($zones as $zone) {
            DeliveryZone::updateOrInsert(
                ['key' => $zone['key']],
                $zone
            );
        }

        $this->command?->info('Seeded '.count($zones).' delivery zones.');
    }
}
