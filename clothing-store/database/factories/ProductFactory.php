<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\SizeChart;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'category_id' => Category::factory(),
            'name' => 'Product '.$counter,
            'name_bn' => 'পণ্য '.$counter,
            'slug' => 'product-'.$counter,
            'sku' => 'SKU-'.str_pad((string) $counter, 6, '0', STR_PAD_LEFT),
            'short_description' => 'Short description for product '.$counter,
            'description' => 'Full description for product '.$counter,
            'fabric' => 'Cotton blend',
            'fit' => 'Regular fit',
            'care' => 'Machine wash cold',
            'price' => rand(500, 5000) * 100,
            'compare_at_price' => rand(100, 100) ? rand(500, 5000) * 100 : null,
            'cost_price' => rand(200, 3000) * 100,
            'is_active' => true,
            'is_featured' => false,
            'is_new' => false,
            'size_chart_id' => null,
            'meta_title' => 'Product '.$counter,
            'meta_description' => 'Meta description for product '.$counter,
            'sold_count' => 0,
            'views' => 0,
            'published_at' => now(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function isNew(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_new' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withSizeChart(SizeChart $sizeChart): static
    {
        return $this->state(fn (array $attributes) => [
            'size_chart_id' => $sizeChart->id,
        ]);
    }

    public function withCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }
}
