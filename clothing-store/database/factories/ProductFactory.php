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
        $uniqueId = uniqid('prod_', true);

        return [
            'category_id' => Category::factory(),
            'name' => 'Product '.$uniqueId,
            'name_bn' => 'পণ্য '.$uniqueId,
            'slug' => 'product-'.$uniqueId,
            'sku' => 'SKU-'.$uniqueId,
            'short_description' => 'Short description for product '.$uniqueId,
            'description' => 'Full description for product '.$uniqueId,
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
            'meta_title' => 'Product '.$uniqueId,
            'meta_description' => 'Meta description for product '.$uniqueId,
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
