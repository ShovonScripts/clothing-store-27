<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SizeChart;
use Illuminate\Database\Eloquent\Factories\Factory;

class SizeChartFactory extends Factory
{
    protected $model = SizeChart::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'name' => 'Size Chart '.$counter,
            'name_bn' => 'সাইজ চার্ট '.$counter,
            'headers' => ['Size', 'Chest (cm)', 'Waist (cm)', 'Hip (cm)'],
            'rows' => [
                ['XS', '86-90', '70-74', '90-94'],
                ['S', '90-94', '74-78', '94-98'],
                ['M', '94-98', '78-82', '98-102'],
                ['L', '98-102', '82-86', '102-106'],
                ['XL', '102-106', '86-90', '106-110'],
            ],
            'is_active' => true,
        ];
    }
}
