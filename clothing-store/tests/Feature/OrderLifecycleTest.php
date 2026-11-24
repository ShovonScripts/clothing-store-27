<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\{
    ApproveOrderAction,
    CancelOrderAction,
    MarkCodCollectedAction,
    MarkDeliveredAction,
    MarkFailedDeliveryAction,
    ProcessOrderAction,
    PlaceOrderAction,
    ReturnOrderAction,
    ShipOrderAction,
    VerifyBkashPaymentAction
};
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
use App\Models\OrderItem;
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

class OrderLifecycleTest extends TestCase
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
    public function cod_order_lifecycle_pending_to_delivered_to_collected(): void
    {
        $owner = User::factory()->create()->assignRole('owner');

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

        $initialStock = $this->variant->stock;

        // Place COD order
        $order = app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(PaymentStatus::Unpaid, $order->payment_status);
        $this->assertEquals(PaymentMethod::Cod, $order->payment_method);

        // Verify stock was reserved
        $this->variant->refresh();
        $this->assertEquals($initialStock - 2, $this->variant->stock);

        // Admin approves order
        $order = app(ApproveOrderAction::class)->execute($order, $owner);
        $this->assertEquals(OrderStatus::Confirmed, $order->status);
        $this->assertEquals($owner->id, $order->approved_by);

        // Admin processes order
        $order = app(ProcessOrderAction::class)->execute($order, $owner);
        $this->assertEquals(OrderStatus::Processing, $order->status);

        // Admin ships order
        $order = app(ShipOrderAction::class)->execute($order, $owner, 'RedX', 'RX123456789');
        $this->assertEquals(OrderStatus::Shipped, $order->status);
        $this->assertEquals('RedX', $order->courier);
        $this->assertEquals('RX123456789', $order->consignment_id);

        // Admin marks as delivered
        $order = app(MarkDeliveredAction::class)->execute($order, $owner);
        $this->assertEquals(OrderStatus::Delivered, $order->status);

        // Admin collects COD
        $order = app(MarkCodCollectedAction::class)->execute($order, $owner);
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);

        // Verify stock was permanently reduced (not restocked)
        $this->variant->refresh();
        $this->assertEquals($initialStock - 2, $this->variant->stock);

        // Verify activity log entries for each transition
        $history = $order->statusHistory;
        $this->assertCount(6, $history); // Pending, Confirmed, Processing, Shipped, Delivered, Paid
    }

    #[Test]
    public function bkash_order_lifecycle_pending_to_verified_to_delivered(): void
    {
        $owner = User::factory()->create()->assignRole('owner');

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

        $initialStock = $this->variant->stock;

        // Place bKash order
        $order = app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(PaymentStatus::AwaitingVerification, $order->payment_status);
        $this->assertEquals(PaymentMethod::BkashSendMoney, $order->payment_method);
        $this->assertEquals('TEST123456', $order->bkash_trxid);

        // Verify stock was reserved
        $this->variant->refresh();
        $this->assertEquals($initialStock - 2, $this->variant->stock);

        // Admin verifies bKash payment
        $order = app(VerifyBkashPaymentAction::class)->execute($order, $owner, 'Verified via admin panel');

        $this->assertEquals(OrderStatus::Confirmed, $order->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);

        // Verify payment record updated
        $payment = $order->payment;
        $this->assertEquals(PaymentStatus::Paid, $payment->status);
        $this->assertEquals($owner->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);

        // Admin processes order
        $order = app(ProcessOrderAction::class)->execute($order, $owner);
        $this->assertEquals(OrderStatus::Processing, $order->status);

        // Admin ships order
        $order = app(ShipOrderAction::class)->execute($order, $owner, 'RedX', 'RX123456789');
        $this->assertEquals(OrderStatus::Shipped, $order->status);

        // Admin marks as delivered
        $order = app(MarkDeliveredAction::class)->execute($order, $owner);
        $this->assertEquals(OrderStatus::Delivered, $order->status);

        // Verify stock was permanently reduced (not restocked)
        $this->variant->refresh();
        $this->assertEquals($initialStock - 2, $this->variant->stock);

        // Verify activity log entries
        $history = $order->statusHistory;
        $this->assertCount(6, $history); // Pending (placed), Confirmed, Processing, Shipped, Delivered
    }
}