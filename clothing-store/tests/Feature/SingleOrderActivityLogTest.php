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
use App\Models\User;
use App\Services\PricingService;
use App\Services\SettingsService;
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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SingleOrderActivityLogTest extends TestCase
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
    }

    #[Test]
    public function single_cod_order_creates_exactly_one_activity_log_and_one_notification_per_admin(): void
    {
        $owner = User::factory()->create()->assignRole('owner');
        $manager = User::factory()->create()->assignRole('manager');
        $staff = User::factory()->create()->assignRole('staff');

        // Verify our 3 admins have the view_orders permission
        $this->assertTrue($owner->hasPermissionTo('view_orders'));
        $this->assertTrue($manager->hasPermissionTo('view_orders'));
        $this->assertTrue($staff->hasPermissionTo('view_orders'));

        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            1,
            $lines,
            '01712345678'
        );

        $data = CheckoutData::fromRequest([
            'name' => 'Test Customer',
            'phone' => '01712345678',
            'email' => 'test@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => '123 Test Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');

        $order = app(PlaceOrderAction::class)->execute($data);

        // Check activity_log table - should have exactly 1 row for this order
        $activityLogCount = DB::table('activity_log')
            ->where('subject_type', Order::class)
            ->where('subject_id', $order->id)
            ->count();

        $this->assertEquals(1, $activityLogCount, 'Exactly one activity log entry should be created for the order');

        // Check notifications - each of the 3 admins created in this test should receive exactly 1 notification
        // (Other tests may create additional users with view_orders permission, so we only verify our 3 admins)
        foreach ([$owner, $manager, $staff] as $admin) {
            $adminNotifications = $admin->notifications()
                ->whereJsonContains('data->order_id', $order->id)
                ->count();
            $this->assertEquals(1, $adminNotifications, "Admin {$admin->name} should receive exactly 1 notification");
        }
    }

    #[Test]
    public function single_bkash_order_creates_exactly_one_activity_log_and_one_notification_per_admin(): void
    {
        $owner = User::factory()->create()->assignRole('owner');
        $manager = User::factory()->create()->assignRole('manager');
        $staff = User::factory()->create()->assignRole('staff');

        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            2,
            $lines,
            '01712345678'
        );

        $data = CheckoutData::fromRequest([
            'name' => 'Test Customer',
            'phone' => '01712345678',
            'email' => 'test@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => '123 Test Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'TEST123456',
            'bkash_sender' => '01711111111',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');

        $order = app(PlaceOrderAction::class)->execute($data);

        $activityLogCount = DB::table('activity_log')
            ->where('subject_type', Order::class)
            ->where('subject_id', $order->id)
            ->count();

        $this->assertEquals(1, $activityLogCount, 'Exactly one activity log entry should be created for the order');

        // Check notifications - each of the 3 admins created in this test should receive exactly 1 notification
        // (Other tests may create additional users with view_orders permission, so we only verify our 3 admins)
        foreach ([$owner, $manager, $staff] as $admin) {
            $adminNotifications = $admin->notifications()
                ->whereJsonContains('data->order_id', $order->id)
                ->count();
            $this->assertEquals(1, $adminNotifications, "Admin {$admin->name} should receive exactly 1 notification");
        }
    }
}
