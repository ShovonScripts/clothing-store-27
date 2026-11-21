<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Black', 'name_bn' => 'কালো', 'hex' => '#000000', 'sort_order' => 1],
            ['name' => 'White', 'name_bn' => 'সাদা', 'hex' => '#FFFFFF', 'sort_order' => 2],
            ['name' => 'Navy', 'name_bn' => 'নেভি', 'hex' => '#000080', 'sort_order' => 3],
            ['name' => 'Olive', 'name_bn' => 'অলিভ', 'hex' => '#808000', 'sort_order' => 4],
            ['name' => 'Grey', 'name_bn' => 'গ্রে', 'hex' => '#808080', 'sort_order' => 5],
            ['name' => 'Maroon', 'name_bn' => 'মারুন', 'hex' => '#800000', 'sort_order' => 6],
            ['name' => 'Sky Blue', 'name_bn' => 'স্কাই ব্লু', 'hex' => '#87CEEB', 'sort_order' => 7],
            ['name' => 'Beige', 'name_bn' => 'বেজ', 'hex' => '#F5F5DC', 'sort_order' => 8],
            ['name' => 'Brown', 'name_bn' => 'ব্রাউন', 'hex' => '#A52A2A', 'sort_order' => 9],
            ['name' => 'Cream', 'name_bn' => 'ক্রিম', 'hex' => '#FFFDD0', 'sort_order' => 10],
            ['name' => 'Charcoal', 'name_bn' => 'চারকোল', 'hex' => '#36454F', 'sort_order' => 11],
        ];

        foreach ($colors as $color) {
            Color::updateOrInsert(
                ['name' => $color['name']],
                $color
            );
        }

        $this->command?->info('Seeded '.count($colors).' colors.');
    }
}
