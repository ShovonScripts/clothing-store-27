<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            DistrictSeeder::class,
            AreaSeeder::class,
            ColorSeeder::class,
            DeliveryZoneSeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            DemoProductSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
