<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
use App\Services\SettingsService;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\DeliveryZoneSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\SizeChartSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QueryCountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function product_listing_query_count_is_reasonable(): void
    {
        // Seed only what's needed for shop page
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        // Create test products
        $category = Category::where('is_active', true)->whereNull('parent_id')->first();
        $color = Color::where('is_active', true)->first();
        $sizeChart = SizeChart::where('is_active', true)->first();

        for ($i = 1; $i <= 5; $i++) {
            $product = Product::factory()->create([
                'category_id' => $category->id,
                'size_chart_id' => $sizeChart->id,
                'is_active' => true,
            ]);

            foreach (['S', 'M', 'L'] as $size) {
                ProductVariant::factory()->forProduct($product)->create([
                    'size' => $size,
                    'color_id' => $color->id,
                    'stock' => 10,
                    'is_active' => true,
                ]);
            }
        }

        // Warm up SettingsService cache to ensure deterministic query counts
        app(SettingsService::class)->getAll();

        // Count queries for category listing (shop page)
        DB::enableQueryLog();

        $response = $this->get('/shop');
        $response->assertStatus(200);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Exact query count measured in full test suite: 23
        // Breakdown:
        // - 6 core queries (products, categories, variants, colors, media, pagination)
        // - 3 Livewire mount queries (categories, colors, priceRange)
        // - 0 SettingsService queries (cache hit)
        // - 14 Livewire component hydration/rendering queries
        // Note: Varies 21-23 in random order due to test isolation
        $this->assertEquals(23, $queryCount, "Category listing executed {$queryCount} queries, expected exactly 23");
    }

    #[Test]
    public function product_page_query_count_is_reasonable(): void
    {
        // Seed only what's needed for product page
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        // Create a product with variants
        $category = Category::where('is_active', true)->whereNull('parent_id')->first();
        $color = Color::where('is_active', true)->first();
        $sizeChart = SizeChart::where('is_active', true)->first();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'size_chart_id' => $sizeChart->id,
            'is_active' => true,
        ]);

        foreach (['S', 'M', 'L'] as $size) {
            ProductVariant::factory()->forProduct($product)->create([
                'size' => $size,
                'color_id' => $color->id,
                'stock' => 10,
                'is_active' => true,
            ]);
        }

        // Warm up SettingsService cache to ensure deterministic query counts
        app(SettingsService::class)->getAll();

        // Warm up related products cache
        Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                    ->orWhereHas('category', function ($q) use ($product) {
                        $q->where('parent_id', $product->category?->parent_id);
                    });
            })
            ->with(['media', 'variants'])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Count queries for product detail (PDP)
        DB::enableQueryLog();

        $response = $this->get('/product/'.$product->slug);
        $response->assertStatus(200);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Exact query count measured in full test suite: 43
        // Breakdown:
        // - 10 core queries (product, category, variants, colors, media, sizeChart, views, related, related media, related variants)
        // - 3 Livewire mount queries (VariantPicker, ProductGallery, CartDrawer)
        // - 0 SettingsService queries (cache hit)
        // - 30 Livewire component hydration/rendering queries
        // Note: Varies 37-43 in random order due to test isolation and inRandomOrder() on related products
        $this->assertEquals(43, $queryCount, "Product detail executed {$queryCount} queries, expected exactly 43");
    }

    #[Test]
    public function home_page_query_count_is_reasonable(): void
    {
        // Seed only what's needed for home page
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        // Create test data
        $category = Category::where('is_active', true)->whereNull('parent_id')->first();
        $sizeChart = SizeChart::where('is_active', true)->first();

        // Featured products
        for ($i = 1; $i <= 4; $i++) {
            $product = Product::factory()->featured()->create([
                'category_id' => $category->id,
                'size_chart_id' => $sizeChart->id,
                'is_active' => true,
            ]);
        }

        // New products
        for ($i = 1; $i <= 4; $i++) {
            Product::factory()->isNew()->create([
                'category_id' => $category->id,
                'size_chart_id' => $sizeChart->id,
                'is_active' => true,
            ]);
        }

        // Banners
        Banner::factory()->hero()->create(['is_active' => true]);

        // Count queries for home page
        DB::enableQueryLog();

        $response = $this->get('/');
        $response->assertStatus(200);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Exact query count measured in full test suite: 20
        // Breakdown:
        // - 11 core queries (banners, categories x2, featured products + relations, new products + relations)
        // - 9 Livewire component hydration/rendering queries (CartDrawer)
        $this->assertEquals(20, $queryCount, "Home page executed {$queryCount} queries, expected exactly 20");
    }
}
