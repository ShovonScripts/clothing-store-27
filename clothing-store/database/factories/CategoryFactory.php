<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'parent_id' => null,
            'name' => 'Category '.$counter,
            'name_bn' => 'বিভাগ '.$counter,
            'slug' => 'category-'.$counter,
            'description' => 'Description for category '.$counter,
            'image_path' => null,
            'sort_order' => $counter,
            'is_active' => true,
            'show_on_home' => true,
            'meta_title' => 'Category '.$counter,
            'meta_description' => 'Meta description for category '.$counter,
        ];
    }

    public function child(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'name' => $attributes['name'].' Child',
            'name_bn' => $attributes['name_bn'].' সন্তান',
            'slug' => $attributes['slug'].'-child',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
