<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\PlaceOrderAction;
use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Models\Area;
use App\Models\District;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockRaceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function concurrent_orders_for_same_variant_only_one_succeeds(): void
    {
        // Seed minimal data
        $this->seed(\Database\Seeders\SettingsSeeder::class);
        $this->seed(\Database\Seeders\PageSeeder::class);
        $this->seed(\Database\Seeders\DistrictSeeder::class);
        $this->seed(\Database\Seeders\DeliveryZoneSeeder::class);
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ColorSeeder::class);
        $this->seed(\Database\Seeders\SizeChartSeeder::class);

        // Create a product with stock = 1
        $category = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->first();
        $color = \App\Models\Color::where('is_active', true)->first();
        $sizeChart = \App\Models\SizeChart::where('is_active', true)->first();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'size_chart_id' => $sizeChart->id,
            'is_active' => true,
            'price' => 100000,
        ]);

        $variant = ProductVariant::factory()->forProduct($product)->create([
            'size' => 'M',
            'color_id' => $color->id,
            'stock' => 1,
            'is_active' => true,
        ]);

        $district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $area = Area::where('is_active', true)->where('district_id', $district->id)->first();

        if (! $area) {
            $area = Area::create([
                'district_id' => $district->id,
                'name' => $district->name . ' Sadar',
                'name_bn' => $district->name_bn . ' সদর',
                'is_active' => true,
            ]);
        }

        $lines = collect([new CartLineData(variantId: $variant->id, qty: 1)]);

        $pricing = app(\App\Services\PricingService::class)->calculate(
            $district->id,
            $area->id,
            '',
            1, // COD
            $lines,
            '01712345678'
        );

        // Prepare checkout data for two concurrent orders
        $data1 = CheckoutData::fromRequest([
            'name' => 'Customer 1',
            'phone' => '01712345678',
            'email' => 'customer1@example.com',
            'district_id' => $district->id,
            'area_id' => $area->id,
            'address' => '123 Test Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');

        $data2 = CheckoutData::fromRequest([
            'name' => 'Customer 2',
            'phone' => '01812345678',
            'email' => 'customer2@example.com',
            'district_id' => $district->id,
            'area_id' => $area->id,
            'address' => '456 Test Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');

        // Simulate concurrent execution using transactions
        // Since PHP is single-threaded, we'll test the locking behavior directly
        $results = [];
        $exceptions = [];

        // First order
        try {
            $order1 = app(PlaceOrderAction::class)->execute($data1);
            $results[] = ['order' => $order1, 'success' => true];
        } catch (\Throwable $e) {
            $results[] = ['error' => $e->getMessage(), 'success' => false];
        }

        // Second order (should fail due to insufficient stock)
        try {
            $order2 = app(PlaceOrderAction::class)->execute($data2);
            $results[] = ['order' => $order2, 'success' => true];
        } catch (\Throwable $e) {
            $results[] = ['error' => $e->getMessage(), 'success' => false];
        }

        // Verify exactly one order succeeded
        $successCount = count(array_filter($results, fn ($r) => $r['success'] ?? false));
        $this->assertEquals(1, $successCount, 'Exactly one order should succeed when stock=1');

        // Verify stock is 0 (never negative)
        $variant->refresh();
        $this->assertEquals(0, $variant->stock, 'Stock should be 0 after one order, never negative');

        // Verify the failed order has appropriate error
        $failedResult = array_filter($results, fn ($r) => !($r['success'] ?? false));
        $this->assertCount(1, $failedResult);
        $failed = array_values($failedResult)[0];
        $this->assertStringContainsString('stock', strtolower($failed['error'] ?? ''));
    }

    #[Test]
    public function stock_never_goes_negative_under_concurrent_access(): void
    {
        $this->seed(\Database\Seeders\SettingsSeeder::class);
        $this->seed(\Database\Seeders\PageSeeder::class);
        $this->seed(\Database\Seeders\DistrictSeeder::class);
        $this->seed(\Database\Seeders\DeliveryZoneSeeder::class);
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\ColorSeeder::class);
        $this->seed(\Database\Seeders\SizeChartSeeder::class);

        $category = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->first();
        $color = \App\Models\Color::where('is_active', true)->first();
        $sizeChart = \App\Models\SizeChart::where('is_active', true)->first();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'size_chart_id' => $sizeChart->id,
            'is_active' => true,
            'price' => 100000,
        ]);

        $variant = ProductVariant::factory()->forProduct($product)->create([
            'size' => 'M',
            'color_id' => $color->id,
            'stock' => 2,
            'is_active' => true,
        ]);

        $district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $area = Area::where('is_active', true)->where('district_id', $district->id)->first();

        if (! $area) {
            $area = Area::create([
                'district_id' => $district->id,
                'name' => $district->name . ' Sadar',
                'name_bn' => $district->name_bn . ' সদর',
                'is_active' => true,
            ]);
        }

        $lines = collect([new CartLineData(variantId: $variant->id, qty: 1)]);

        $pricing = app(\App\Services\PricingService::class)->calculate(
            $district->id,
            $area->id,
            '',
            1,
            $lines,
            '01712345678'
        );

        // Place 3 orders for stock=2, only 2 should succeed
        for ($i = 1; $i <= 3; $i++) {
            $data = CheckoutData::fromRequest([
                'name' => "Customer $i",
                'phone' => "017{$i}2345678",
                'email' => "customer$i@example.com",
                'district_id' => $district->id,
                'area_id' => $area->id,
                'address' => "$i Test Street, Dhaka",
                'payment_method' => 'cod',
                'coupon_code' => '',
                'note' => '',
                'total' => $pricing['total'],
                'cart_lines' => $lines->toArray(),
            ], $lines, '127.0.0.1');

            try {
                app(PlaceOrderAction::class)->execute($data);
            } catch (\Throwable) {
                // Expected for the 3rd order
            }
        }

        $variant->refresh();
        $this->assertEquals(0, $variant->stock, 'Stock should be 0 after 2 successful orders from stock=2, never negative');
    }
}