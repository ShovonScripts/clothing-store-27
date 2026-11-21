<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $hexColors = [
            '#000000', '#FFFFFF', '#FF0000', '#0000FF', '#00FF00',
            '#FFFF00', '#FF00FF', '#00FFFF', '#FFA500', '#800080',
            '#A52A2A',
        ];

        return [
            'name' => 'Color '.$counter,
            'name_bn' => 'রঙ '.$counter,
            'hex' => $hexColors[($counter - 1) % count($hexColors)],
            'sort_order' => $counter,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
