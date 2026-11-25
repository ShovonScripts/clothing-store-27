<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\PlaceOrderAction;
use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Models\Area;
use App\Models\Category;
use App\Models\Color;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
use App\Services\ActivityLogger;
use App\Services\BlacklistService;
use App\Services\DailyCounterService;
use App\Services\OrderNumberService;
use App\Services\OrderStatusService;
use App\Services\PricingService;
use App\Services\SettingsService;
use App\Services\StockService;
use Database\Seeders\AreaSeeder;
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
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlaceOrderActionQueryTest extends TestCase
{
    use RefreshDatabase;

    protected District $district;

    protected Area $area;

    protected Color $color;

    protected SizeChart $sizeChart;

    protected Product $product;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(AreaSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $this->district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $this->area = Area::where('is_active', true)->where('district_id', $this->district->id)->first();
        $this->color = Color::where('is_active', true)->first();
        $this->sizeChart = SizeChart::where('is_active', true)->first();

        $this->product = Product::factory()->create([
            'category_id' => Category::where('is_active', true)->whereNull('parent_id')->first()->id,
            'size_chart_id' => $this->sizeChart->id,
            'is_active' => true,
            'price' => 100000,
        ]);

        $this->variant = ProductVariant::factory()->forProduct($this->product)->create([
            'size' => 'M',
            'color_id' => $this->color->id,
            'stock' => 10,
            'is_active' => true,
        ]);

        session(['cart' => [$this->variant->id => 2]]);

        app(SettingsService::class)->getAll();

        // Ensure query log is disabled before each test
        DB::disableQueryLog();
        DB::getQueryLog(); // Clear any residual
    }

    #[Test]
    public function place_order_action_queries_logged(): void
    {
        // Ensure query log is completely disabled before starting
        DB::disableQueryLog();
        DB::getQueryLog(); // Clear any residual

        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $area = Area::where('is_active', true)->where('district_id', $district->id)->first();

        $pricing = app(PricingService::class)->calculate(
            $district->id,
            $area->id,
            '',
            1,
            $lines,
            '01712345678'
        );

        $data = CheckoutData::fromRequest([
            'name' => 'Test Customer',
            'phone' => '01712345678',
            'email' => 'test@example.com',
            'district_id' => $district->id,
            'area_id' => $area->id,
            'address' => '123 Test Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');

        // Warm up all caches
        app(SettingsService::class)->getAll();
        app(PricingService::class)->calculate(
            $district->id,
            $area->id,
            '',
            1,
            $lines,
            '01712345678'
        );
        app(ActivityLogger::class);
        app(DailyCounterService::class)->next(now()->toDateString());
        app(StockService::class);
        app(BlacklistService::class);
        app(OrderNumberService::class);
        app(OrderStatusService::class);

        // Warmup run
        $warmupData = CheckoutData::fromRequest([
            'name' => 'Warmup Customer',
            'phone' => '01711111111',
            'email' => 'warmup@example.com',
            'district_id' => $district->id,
            'area_id' => $area->id,
            'address' => 'Warmup Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        app(PlaceOrderAction::class)->execute($warmupData);

        // Use Queue::fake() to prevent queued notifications from running
        // This isolates the base order placement queries from notification side-effects
        Queue::fake();

        // Use connection query log with proper clearing - disableQueryLog() clears the array
        $conn = DB::connection();
        $conn->disableQueryLog(); // This clears the query log array
        $conn->enableQueryLog();  // Starts fresh empty array

        // Count queries for PlaceOrderAction execution (second run, fully warm)
        $order = app(PlaceOrderAction::class)->execute($data);

        $queries = $conn->getQueryLog();
        $conn->disableQueryLog();

        // Print all queries with table names
        foreach ($queries as $i => $q) {
            $sql = $q['query'];
            $table = 'unknown';
            if (preg_match('/from\s+`(\w+)`/i', $sql, $m) || preg_match('/into\s+`(\w+)`/i', $sql, $m) || preg_match('/update\s+`(\w+)`/i', $sql, $m) || preg_match('/delete from\s+`(\w+)`/i', $sql, $m)) {
                $table = $m[1];
            }
            echo 'Query '.($i + 1)." [$table]: ".$sql."\n";
        }

        echo "\nTotal queries (base order, excluding queued notifications): ".count($queries)."\n";

        // Base order placement is 29 queries (without users query from sync listener when Queue::fake())
        // With Queue::fake(), the queued notification job is captured but not executed
        $this->assertEquals(29, count($queries), 'Expected 29 queries, got '.count($queries));
    }

    #[Test]
    public function place_order_action_queries_with_queued_notifications(): void
    {
        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $area = Area::where('is_active', true)->where('district_id', $district->id)->first();

        $pricing = app(PricingService::class)->calculate(
            $district->id,
            $area->id,
            '',
            1,
            $lines,
            '01712345678'
        );

        $data = CheckoutData::fromRequest([
            'name' => 'Test Customer 2',
            'phone' => '01712345679',
            'email' => 'test2@example.com',
            'district_id' => $district->id,
            'area_id' => $area->id,
            'address' => '456 Test Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');

        // Use connection query log with proper clearing
        $conn = DB::connection();
        $conn->disableQueryLog(); // This clears the query log array
        $conn->enableQueryLog();  // Starts fresh empty array

        // Don't fake the queue - let sync queue run queued notifications immediately
        $order = app(PlaceOrderAction::class)->execute($data);

        $queries = $conn->getQueryLog();
        $conn->disableQueryLog();

        // Count queries by table and operation
        $stats = [];
        foreach ($queries as $q) {
            $sql = $q['query'];
            $table = 'unknown';
            $op = 'unknown';
            if (preg_match('/^\s*select/i', $sql)) {
                $op = 'SELECT';
            } elseif (preg_match('/^\s*insert/i', $sql)) {
                $op = 'INSERT';
            } elseif (preg_match('/^\s*update/i', $sql)) {
                $op = 'UPDATE';
            } elseif (preg_match('/^\s*delete/i', $sql)) {
                $op = 'DELETE';
            }
            if (preg_match('/from\s+`(\w+)`/i', $sql, $m) || preg_match('/into\s+`(\w+)`/i', $sql, $m) || preg_match('/update\s+`(\w+)`/i', $sql, $m) || preg_match('/delete from\s+`(\w+)`/i', $sql, $m)) {
                $table = $m[1];
            }
            $key = "$table.$op";
            $stats[$key] = ($stats[$key] ?? 0) + 1;
        }

        ksort($stats);
        foreach ($stats as $key => $count) {
            echo "$key: $count\n";
        }

        $total = count($queries);
        echo "\nTotal queries (base + queued notifications): $total\n";

        // With sync queue, queued notifications run immediately
        // Base = 29 queries, plus notification inserts for each admin with view_orders permission
        // Total varies based on how many admins exist in the database
        // We only assert the base is 29 (verified in first test)
        $this->assertGreaterThanOrEqual(29, $total, 'Total queries should be at least base count');
    }
}
