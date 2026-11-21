<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'title' => 'Banner '.$counter,
            'subtitle' => 'Subtitle for banner '.$counter,
            'button_text' => 'Shop Now',
            'button_link' => '/shop',
            'image_path' => null,
            'placement' => 'hero',
            'sort_order' => $counter,
            'is_active' => true,
        ];
    }

    public function hero(): static
    {
        return $this->state(fn (array $attributes) => [
            'placement' => 'hero',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
