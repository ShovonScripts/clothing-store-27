<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\PlaceOrderAction;
use App\Console\Commands\ExpireUnverifiedBkashPayments;
use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpireUnverifiedBkashTest extends TestCase
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
    public function expire_command_cancels_only_unverified_bkash_older_than_threshold(): void
    {
        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            2,
            $lines,
            '01712345678'
        );

        // Create old unverified bKash order (25 hours ago)
        $oldBkashData = CheckoutData::fromRequest([
            'name' => 'Old Bkash Customer',
            'phone' => '01711111111',
            'email' => 'oldbkash@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Old Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'OLD123456',
            'bkash_sender' => '01711111111',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $oldBkashOrder = app(PlaceOrderAction::class)->execute($oldBkashData);
        $oldBkashOrder->created_at = now()->subHours(25);
        $oldBkashOrder->save();

        // Create recent unverified bKash order (12 hours ago) - should NOT be cancelled
        $recentBkashData = CheckoutData::fromRequest([
            'name' => 'Recent Bkash Customer',
            'phone' => '01722222222',
            'email' => 'recentbkash@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Recent Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'RECENT1234',
            'bkash_sender' => '01722222222',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $recentBkashOrder = app(PlaceOrderAction::class)->execute($recentBkashData);
        $recentBkashOrder->created_at = now()->subHours(12);
        $recentBkashOrder->save();

        // Create COD order (25 hours ago) - should NOT be cancelled
        $codData = CheckoutData::fromRequest([
            'name' => 'COD Customer',
            'phone' => '01733333333',
            'email' => 'cod@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'COD Street, Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $codOrder = app(PlaceOrderAction::class)->execute($codData);
        $codOrder->created_at = now()->subHours(25);
        $codOrder->save();

        // Create verified bKash order (25 hours ago) - should NOT be cancelled
        $verifiedBkashData = CheckoutData::fromRequest([
            'name' => 'Verified Bkash Customer',
            'phone' => '01744444444',
            'email' => 'verifiedbkash@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Verified Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'VERIFIED12',
            'bkash_sender' => '01744444444',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $verifiedBkashOrder = app(PlaceOrderAction::class)->execute($verifiedBkashData);
        $verifiedBkashOrder->created_at = now()->subHours(25);
        $verifiedBkashOrder->payment_status = PaymentStatus::Paid;
        $verifiedBkashOrder->status = OrderStatus::Confirmed;
        $verifiedBkashOrder->save();

        // Run command with 24-hour threshold
        $exitCode = Artisan::call('orders:expire-unverified-bkash', ['--hours' => 24]);
        
        // Debug: output command output
        $output = Artisan::output();
        dump($output);

        $this->assertEquals(0, $exitCode, "Command failed with exit code $exitCode. Output: $output");

        // Verify only old unverified bKash was cancelled
        $oldBkashOrder->refresh();
        $recentBkashOrder->refresh();
        $codOrder->refresh();
        $verifiedBkashOrder->refresh();

        $this->assertEquals(OrderStatus::Cancelled, $oldBkashOrder->status, 'Old unverified bKash should be cancelled');
        $this->assertEquals(OrderStatus::Pending, $recentBkashOrder->status, 'Recent unverified bKash should remain pending');
        $this->assertEquals(OrderStatus::Pending, $codOrder->status, 'COD order should remain pending');
        $this->assertEquals(OrderStatus::Confirmed, $verifiedBkashOrder->status, 'Verified bKash should remain confirmed');
    }

    #[Test]
    public function expire_command_releases_stock_on_cancelled_orders(): void
    {
        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 3)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            2,
            $lines,
            '01712345678'
        );

        $bkashData = CheckoutData::fromRequest([
            'name' => 'Stock Test Customer',
            'phone' => '01755555555',
            'email' => 'stocktest@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Stock Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'STOCK12345',
            'bkash_sender' => '01755555555',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $order = app(PlaceOrderAction::class)->execute($bkashData);
        $order->created_at = now()->subHours(25);
        $order->save();

        $initialStock = $this->variant->fresh()->stock;
        $this->assertEquals(7, $initialStock, 'Stock should be reduced after order');

        // Run expire command
        Artisan::call('orders:expire-unverified-bkash', ['--hours' => 24]);

        // Stock should be restored
        $restoredStock = $this->variant->fresh()->stock;
        $this->assertEquals(10, $restoredStock, 'Stock should be restored after cancellation');
    }

    #[Test]
    public function expire_command_dry_run_makes_no_changes(): void
    {
        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            2,
            $lines,
            '01712345678'
        );

        $bkashData = CheckoutData::fromRequest([
            'name' => 'Dry Run Customer',
            'phone' => '01766666666',
            'email' => 'dryrun@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Dry Run Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'DRYRUN1234',
            'bkash_sender' => '01766666666',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $order = app(PlaceOrderAction::class)->execute($bkashData);
        $order->created_at = now()->subHours(25);
        $order->save();

        // Run command in dry-run mode
        $exitCode = Artisan::call('orders:expire-unverified-bkash', ['--hours' => 24, '--dry-run' => true]);

        $this->assertEquals(0, $exitCode);

        // Order should NOT be cancelled
        $order->refresh();
        $this->assertEquals(OrderStatus::Pending, $order->status, 'Order should not be cancelled in dry-run mode');
        $this->assertEquals(PaymentStatus::AwaitingVerification, $order->payment_status);

        // Stock should NOT be restored
        $stock = $this->variant->fresh()->stock;
        $this->assertEquals(8, $stock, 'Stock should not change in dry-run mode');
    }

    #[Test]
    public function expire_command_with_custom_hours_threshold(): void
    {
        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            2,
            $lines,
            '01712345678'
        );

        // Create order 12 hours ago
        $bkashData = CheckoutData::fromRequest([
            'name' => 'Custom Hours Customer',
            'phone' => '01777777777',
            'email' => 'customhours@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Custom Hours Street, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'CUSTOM1234',
            'bkash_sender' => '01777777777',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $order = app(PlaceOrderAction::class)->execute($bkashData);
        $order->created_at = now()->subHours(12);
        $order->save();

        // Run with 6-hour threshold - should cancel
        Artisan::call('orders:expire-unverified-bkash', ['--hours' => 6]);
        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status, 'Should be cancelled with 6-hour threshold');

        // Create another order 12 hours ago
        $bkashData2 = CheckoutData::fromRequest([
            'name' => 'Custom Hours Customer 2',
            'phone' => '01788888888',
            'email' => 'customhours2@example.com',
            'district_id' => $this->district->id,
            'area_id' => $this->area->id,
            'address' => 'Custom Hours Street 2, Dhaka',
            'payment_method' => 'bkash_send_money',
            'bkash_trxid' => 'CUSTOM1235',
            'bkash_sender' => '01788888888',
            'bkash_amount' => $pricing['total'],
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
        $order2 = app(PlaceOrderAction::class)->execute($bkashData2);
        $order2->created_at = now()->subHours(12);
        $order2->save();

        // Run with 24-hour threshold - should NOT cancel
        Artisan::call('orders:expire-unverified-bkash', ['--hours' => 24]);
        $order2->refresh();
        $this->assertEquals(OrderStatus::Pending, $order2->status, 'Should not be cancelled with 24-hour threshold');
    }
}