<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'];

        return [
            'product_id' => Product::factory(),
            'sku' => 'VAR-'.str_pad((string) $counter, 8, '0', STR_PAD_LEFT),
            'size' => $sizes[($counter - 1) % count($sizes)],
            'color_id' => Color::factory(),
            'price' => null,
            'stock' => rand(1, 50),
            'low_stock_threshold' => 5,
            'is_active' => true,
            'sort_order' => $counter,
        ];
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }

    public function size(string $size): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $size,
        ]);
    }

    public function color(Color $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color_id' => $color->id,
        ]);
    }

    public function price(int $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }

    public function stock(int $stock): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $stock,
        ]);
    }

    public function lowStock(int $threshold = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $threshold,
            'low_stock_threshold' => $threshold,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
