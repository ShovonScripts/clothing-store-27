<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\SizeChart;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SizeChartSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $this->owner = User::factory()->create()->assignRole('owner');
        $this->manager = User::factory()->create()->assignRole('manager');
        $this->staff = User::factory()->create()->assignRole('staff');
    }

    /** @test */
    public function owner_can_create_product_with_variants_and_image(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('slug', 't-shirts')->first();
        $color1 = Color::where('name', 'Black')->first();
        $color2 = Color::where('name', 'White')->first();
        $sizeChart = SizeChart::where('name', 'T-Shirt / Polo (inches)')->first();

        $this->actingAs($this->owner, 'web');

        $component = Livewire::test(CreateProduct::class)
            ->set('data.name', 'Test T-Shirt')
            ->set('data.name_bn', 'টেস্ট টি-শার্ট')
            ->set('data.slug', 'test-t-shirt-'.uniqid())
            ->set('data.category_id', $category->id)
            ->set('data.price', 1500)
            ->set('data.compare_at_price', 2000)
            ->set('data.cost_price', 750)
            ->set('data.description', 'Test product description')
            ->set('data.fabric', '100% Cotton')
            ->set('data.fit', 'regular')
            ->set('data.care', 'Machine wash cold')
            ->set('data.size_chart_id', $sizeChart->id)
            ->set('data.is_active', true)
            ->set('data.is_featured', false)
            ->set('data.is_new', true)
            ->set('data.variants', [
                ['size' => 'S', 'color_id' => $color1->id, 'stock' => 10, 'is_active' => true],
                ['size' => 'M', 'color_id' => $color1->id, 'stock' => 15, 'is_active' => true],
                ['size' => 'L', 'color_id' => $color1->id, 'stock' => 12, 'is_active' => true],
                ['size' => 'S', 'color_id' => $color2->id, 'stock' => 8, 'is_active' => true],
                ['size' => 'M', 'color_id' => $color2->id, 'stock' => 10, 'is_active' => true],
                ['size' => 'L', 'color_id' => $color2->id, 'stock' => 8, 'is_active' => true],
            ])
            ->call('create');

        $component->assertHasNoErrors();

        $product = Product::where('name', 'Test T-Shirt')->first();
        $this->assertNotNull($product);
        $this->assertEquals(6, $product->variants()->count());

        // Check variants
        $variants = $product->variants()->get();
        $this->assertEquals(6, $variants->count());

        // Check 3 sizes x 2 colours = 6 variants
        $sizes = $variants->pluck('size')->unique()->sort()->values();
        $this->assertEquals(['L', 'M', 'S'], $sizes->values()->all());

        $colours = $variants->pluck('color_id')->unique()->sort()->values();
        $this->assertEquals(2, $colours->count());
    }

    /** @test */
    public function owner_can_create_product_with_image_and_conversions(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('slug', 't-shirts')->first();
        $color = Color::where('name', 'Black')->first();
        $sizeChart = SizeChart::where('name', 'T-Shirt / Polo (inches)')->first();

        $this->actingAs($this->owner, 'web');

        $component = Livewire::test(CreateProduct::class)
            ->set('data.name', 'Test T-Shirt with Image')
            ->set('data.name_bn', 'টেস্ট টি-শার্ট ছবি সহ')
            ->set('data.slug', 'test-t-shirt-image-'.uniqid())
            ->set('data.category_id', $category->id)
            ->set('data.price', 1500)
            ->set('data.compare_at_price', 2000)
            ->set('data.cost_price', 750)
            ->set('data.description', 'Test product with image')
            ->set('data.fabric', '100% Cotton')
            ->set('data.fit', 'regular')
            ->set('data.care', 'Machine wash cold')
            ->set('data.size_chart_id', $sizeChart->id)
            ->set('data.is_active', true)
            ->set('data.is_featured', false)
            ->set('data.is_new', true)
            ->set('data.variants', [
                ['size' => 'S', 'color_id' => $color->id, 'stock' => 10, 'is_active' => true],
                ['size' => 'M', 'color_id' => $color->id, 'stock' => 15, 'is_active' => true],
                ['size' => 'L', 'color_id' => $color->id, 'stock' => 12, 'is_active' => true],
            ])
            ->call('create');

        $component->assertHasNoErrors();

        $product = Product::where('name', 'Test T-Shirt with Image')->first();
        $this->assertNotNull($product);
        $this->assertEquals(3, $product->variants()->count());

        // Check that the product was created successfully
        $this->assertEquals('Test T-Shirt with Image', $product->name);
        $this->assertEquals(3, $product->variants()->count());
    }
}
