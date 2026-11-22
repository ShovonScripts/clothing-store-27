<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
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

        // Count queries for category listing (shop page)
        DB::enableQueryLog();

        $response = $this->get('/shop');
        $response->assertStatus(200);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Exact query count measured in isolation: 26
        // Note: In full test suite with other tests, count may be 28 due to additional data
        // Target: <= 30 queries
        $this->assertLessThanOrEqual(30, $queryCount, "Category listing executed {$queryCount} queries, expected <= 30");
        $this->assertGreaterThanOrEqual(20, $queryCount, "Category listing executed {$queryCount} queries, expected >= 20");
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

        // Count queries for product detail (PDP)
        DB::enableQueryLog();

        $response = $this->get('/product/'.$product->slug);
        $response->assertStatus(200);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Exact query count measured in isolation: 46
        // Note: In full test suite with other tests, count may be 48-50 due to additional data
        // Target: <= 50 queries
        $this->assertLessThanOrEqual(50, $queryCount, "Product detail executed {$queryCount} queries, expected <= 50");
        $this->assertGreaterThanOrEqual(35, $queryCount, "Product detail executed {$queryCount} queries, expected >= 35");
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

        // Target: <= 20 queries
        $this->assertLessThanOrEqual(20, $queryCount, "Home page executed {$queryCount} queries, expected <= 20");
        $this->assertGreaterThanOrEqual(10, $queryCount, "Home page executed {$queryCount} queries, expected >= 10");
    }
}
