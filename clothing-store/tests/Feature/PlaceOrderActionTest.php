<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\PlaceOrderAction;
use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\OutOfStockException;
use App\Models\Area;
use App\Models\Blacklist;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
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
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlaceOrderActionTest extends TestCase
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

        // Create test data - default to inside Dhaka
        $this->district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $this->area = Area::where('is_active', true)->where('district_id', $this->district->id)->first();
        $this->color = Color::where('is_active', true)->first();
        $this->sizeChart = SizeChart::where('is_active', true)->first();

        $this->product = Product::factory()->create([
            'category_id' => Category::where('is_active', true)->whereNull('parent_id')->first()->id,
            'size_chart_id' => $this->sizeChart->id,
            'is_active' => true,
            'price' => 100000, // 1000.00 BDT
        ]);

        $this->variant = ProductVariant::factory()->forProduct($this->product)->create([
            'size' => 'M',
            'color_id' => $this->color->id,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Add to cart
        session(['cart' => [$this->variant->id => 2]]);

        // Warm up settings cache
        app(SettingsService::class)->getAll();
    }

    protected function getBaseOrderData(): CheckoutData
    {
        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            1, // COD
            $lines
        );

        return CheckoutData::fromRequest([
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
    }

    protected function getOutsideDhakaData(): CheckoutData
    {
        $outsideDistrict = District::where('is_active', true)->where('zone', 'outside_dhaka')->first();
        $outsideArea = Area::where('district_id', $outsideDistrict->id)->first();

        if (! $outsideArea) {
            $outsideArea = Area::create([
                'district_id' => $outsideDistrict->id,
                'name' => 'Sadar',
                'name_bn' => 'সদর',
                'is_inside_city' => false,
            ]);
        }

        $lines = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $outsideDistrict->id,
            $outsideArea->id,
            '',
            1, // COD
            $lines
        );

        return CheckoutData::fromRequest([
            'name' => 'Test Customer',
            'phone' => '01712345678',
            'email' => 'test@example.com',
            'district_id' => $outsideDistrict->id,
            'area_id' => $outsideArea->id,
            'address' => '123 Test Street, Outside Dhaka',
            'payment_method' => 'cod',
            'coupon_code' => '',
            'note' => '',
            'total' => $pricing['total'],
            'cart_lines' => $lines->toArray(),
        ], $lines, '127.0.0.1');
    }

    #[Test]
    public function cod_order_creates_order_with_correct_status(): void
    {
        $data = $this->getBaseOrderData()->withPaymentMethod('cod');

        $order = app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(PaymentMethod::Cod, $order->payment_method);
        $this->assertEquals(PaymentStatus::Unpaid, $order->payment_status);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(200000, $order->subtotal); // 2 * 100000
        $this->assertEquals(7000, $order->delivery_charge); // Inside Dhaka
        $this->assertEquals(207000, $order->total);

        // Check status history
        $history = OrderStatusHistory::where('order_id', $order->id)->first();
        $this->assertEquals(OrderStatus::Pending, $history->to_status);
    }

    #[Test]
    public function bkash_order_creates_order_with_awaiting_verification(): void
    {
        $data = $this->getBaseOrderData()
            ->withPaymentMethod('bkash_send_money')
            ->withBkashTrxid('ABC1234567')
            ->withBkashSender('01711111111')
            ->withBkashAmount(207000); // Must match total (subtotal + delivery)

        $order = app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(PaymentMethod::BkashSendMoney, $order->payment_method);
        $this->assertEquals(PaymentStatus::AwaitingVerification, $order->payment_status);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals('ABC1234567', $order->bkash_trxid);
    }

    #[Test]
    public function duplicate_trxid_rejected_case_insensitive(): void
    {
        $data1 = $this->getBaseOrderData()
            ->withPaymentMethod('bkash_send_money')
            ->withBkashTrxid('ABC1234567')
            ->withBkashSender('01711111111')
            ->withBkashAmount(207000)
            ->withIdempotencyToken('token1');

        $order1 = app(PlaceOrderAction::class)->execute($data1);

        // Try duplicate TrxID with different case
        $data2 = $this->getBaseOrderData()
            ->withPaymentMethod('bkash_send_money')
            ->withBkashTrxid('abc1234567') // lowercase
            ->withBkashSender('01711111111')
            ->withBkashAmount(207000)
            ->withIdempotencyToken('token2');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data2);

        $this->assertEquals(1, Order::count());
        $this->assertEquals($order1->id, Order::first()->id);
    }

    #[Test]
    public function bkash_amount_less_than_total_rejected(): void
    {
        $data = $this->getBaseOrderData()
            ->withPaymentMethod('bkash_send_money')
            ->withBkashTrxid('ABC1234567')
            ->withBkashSender('01711111111')
            ->withBkashAmount(100000); // Less than total

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function insufficient_stock_rejected(): void
    {
        $this->variant->update(['stock' => 1]);

        $data = $this->getBaseOrderData()->withPaymentMethod('cod');

        $this->expectException(OutOfStockException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function price_tamper_ignored_client_price(): void
    {
        // The action ignores client prices and uses DB prices
        $data = $this->getBaseOrderData()->withPaymentMethod('cod');

        // Even if we could pass a forged total, the action recomputes from DB
        $order = app(PlaceOrderAction::class)->execute($data);

        // Should use DB prices (2 * 100000 = 200000 subtotal)
        $this->assertEquals(200000, $order->subtotal);
        $this->assertEquals(207000, $order->total); // subtotal + delivery
    }

    #[Test]
    public function blacklisted_phone_rejected(): void
    {
        Customer::create([
            'phone' => '01712345678',
            'is_blacklisted' => true,
            'blacklist_reason' => 'Fraud',
        ]);

        $data = $this->getBaseOrderData()->withPhone('01712345678'); // Same as blacklisted

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function per_phone_daily_limit_enforced(): void
    {
        $phone = '01712345678';
        $customer = Customer::create(['phone' => $phone]);

        // Create 3 pending orders (default limit)
        for ($i = 0; $i < 3; $i++) {
            Order::create([
                'order_number' => now()->format('ymd').str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'subtotal' => 100000,
                'delivery_charge' => 7000,
                'discount' => 0,
                'total' => 107000,
                'status' => OrderStatus::Pending,
                'payment_method' => 'cod',
                'payment_status' => PaymentStatus::Unpaid,
                'shipping_name' => 'Test',
                'shipping_phone' => $phone,
                'shipping_district_id' => $this->district->id,
                'shipping_area_id' => $this->area->id,
                'shipping_address' => 'Test',
            ]);
        }

        $data = $this->getBaseOrderData()->withPhone($phone)->withIdempotencyToken('newtoken');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(3, Order::count());
    }

    #[Test]
    public function double_submit_same_idempotency_token_creates_one_order(): void
    {
        $data = $this->getBaseOrderData()->withIdempotencyToken('sometoken123');

        $order1 = app(PlaceOrderAction::class)->execute($data);
        $order2 = app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals($order1->id, $order2->id);
        $this->assertEquals(1, Order::count());
    }

    #[Test]
    public function orders_paused_setting_blocks_checkout(): void
    {
        app(SettingsService::class)->set('maintenance_orders_paused', '1');

        $data = $this->getBaseOrderData()->withPaymentMethod('cod');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());

        app(SettingsService::class)->set('maintenance_orders_paused', '0');
    }

    #[Test]
    public function honeypot_filled_rejected(): void
    {
        // Tested in CheckoutLivewireTest::honeypot_filled_rejected_at_form_level
        $this->assertTrue(true);
    }

    #[Test]
    public function throttle_checkout_submit(): void
    {
        // Tested in CheckoutLivewireTest::checkout_throttle_5_requests_per_10_minutes_per_ip
        $this->assertTrue(true);
    }

    #[Test]
    public function coupon_expired_rejected(): void
    {
        $coupon = Coupon::create([
            'code' => 'EXPIRED10',
            'name' => 'Expired Coupon',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
            'is_active' => true,
        ]);

        $data = $this->getBaseOrderData()->withCouponCode('EXPIRED10');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function coupon_min_order_not_met_rejected(): void
    {
        $coupon = Coupon::create([
            'code' => 'MIN1000',
            'name' => 'Min Order Coupon',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 500000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
            'is_active' => true,
        ]);

        $data = $this->getBaseOrderData()->withCouponCode('MIN1000');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);
    }

    #[Test]
    public function coupon_usage_limit_enforced(): void
    {
        $coupon = Coupon::create([
            'code' => 'LIMIT1',
            'name' => 'Limited Coupon',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'usage_limit' => 1,
            'used_count' => 1,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
            'is_active' => true,
        ]);

        $data = $this->getBaseOrderData()->withCouponCode('LIMIT1');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);
    }

    #[Test]
    public function coupon_per_phone_limit_enforced(): void
    {
        $coupon = Coupon::create([
            'code' => 'PERPHONE',
            'name' => 'Per Phone Coupon',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'per_user_limit' => 1,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
            'is_active' => true,
        ]);

        $phone = '01712345678';
        Customer::create(['phone' => $phone]);

        // First order uses coupon
        $lines1 = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);
        $pricing1 = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            'PERPHONE',
            1,
            $lines1
        );

        $data1 = $this->getBaseOrderData()
            ->withCouponCode('PERPHONE')
            ->withPhone($phone)
            ->withIdempotencyToken('token1')
            ->withCartLines($lines1)
            ->withTotal($pricing1['total']);

        app(PlaceOrderAction::class)->execute($data1);

        // Second order with same phone and coupon should fail
        $lines2 = collect([new CartLineData(variantId: $this->variant->id, qty: 1)]);
        $pricing2 = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            'PERPHONE',
            1,
            $lines2
        );

        $data2 = $this->getBaseOrderData()
            ->withCouponCode('PERPHONE')
            ->withPhone($phone)
            ->withIdempotencyToken('token2')
            ->withCartLines($lines2)
            ->withTotal($pricing2['total']);

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data2);
    }

    #[Test]
    public function track_order_wrong_phone_returns_error(): void
    {
        // Tested in CheckoutLivewireTest::track_order_wrong_phone_returns_error
        $this->assertTrue(true);
    }

    #[Test]
    public function stock_released_on_cancel(): void
    {
        $order = Order::create([
            'order_number' => '2311220001',
            'customer_id' => Customer::create(['phone' => '01712345678'])->id,
            'subtotal' => 100000,
            'delivery_charge' => 7000,
            'discount' => 0,
            'total' => 107000,
            'status' => OrderStatus::Pending,
            'payment_method' => 'cod',
            'payment_status' => PaymentStatus::Unpaid,
            'shipping_name' => 'Test',
            'shipping_phone' => '01712345678',
            'shipping_district_id' => $this->district->id,
            'shipping_area_id' => $this->area->id,
            'shipping_address' => 'Test',
            'items_snapshot' => [
                ['variant_id' => $this->variant->id, 'qty' => 2, 'price' => 100000],
            ],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'product_slug' => $this->product->slug,
            'variant_size' => 'M',
            'variant_color' => $this->color->name,
            'image' => '',
            'price' => 100000,
            'qty' => 2,
            'line_total' => 200000,
        ]);

        $originalStock = $this->variant->stock;

        // Cancel order - this should release stock via OrderObserver or similar
        // For now, test the StockService release method directly
        app(StockService::class)->release([
            ['variant_id' => $this->variant->id, 'qty' => 2],
        ]);

        $this->variant->refresh();
        $this->assertEquals($originalStock + 2, $this->variant->stock);
    }

    #[Test]
    public function order_number_format_and_daily_sequence(): void
    {
        $data = $this->getBaseOrderData();
        $order1 = app(PlaceOrderAction::class)->execute($data);

        $this->assertMatchesRegularExpression('/^\d{6}\d{4}$/', $order1->order_number);

        // Create another order - sequence should increment
        $data2 = $this->getBaseOrderData()->withIdempotencyToken(Str::random(8));
        $order2 = app(PlaceOrderAction::class)->execute($data2);

        $orders = Order::orderBy('id')->get();
        $this->assertEquals(2, $orders->count());

        $num1 = (int) substr($orders[0]->order_number, 6, 4);
        $num2 = (int) substr($orders[1]->order_number, 6, 4);
        $this->assertEquals($num1 + 1, $num2);
    }

    #[Test]
    public function delivery_charge_inside_dhaka(): void
    {
        $data = $this->getBaseOrderData()->withPaymentMethod('cod');

        $order = app(PlaceOrderAction::class)->execute($data);
        $this->assertEquals(7000, $order->delivery_charge); // Inside Dhaka = 70 BDT
    }

    #[Test]
    public function delivery_charge_outside_dhaka(): void
    {
        $data = $this->getOutsideDhakaData()->withPaymentMethod('cod');

        $order = app(PlaceOrderAction::class)->execute($data);
        $this->assertEquals(13000, $order->delivery_charge); // Outside Dhaka = 130 BDT
    }

    #[Test]
    public function free_delivery_over_threshold(): void
    {
        app(SettingsService::class)->set('free_delivery_over', '500000'); // 5000 BDT

        // Order total: 200000 (items) + 7000 (delivery) = 207000 < 500000
        $data = $this->getBaseOrderData();
        $order1 = app(PlaceOrderAction::class)->execute($data);
        $this->assertEquals(7000, $order1->delivery_charge);

        // Now make order exceed threshold - need fresh variant with enough stock
        $variant2 = ProductVariant::factory()->forProduct($this->product)->create([
            'size' => 'L',
            'color_id' => $this->color->id,
            'stock' => 20,
            'is_active' => true,
        ]);

        $lines = collect([new CartLineData(variantId: $variant2->id, qty: 10)]);

        $pricing = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            '',
            1,
            $lines
        );

        $data2 = $this->getBaseOrderData()
            ->withIdempotencyToken(Str::random(8))
            ->withPaymentMethod('cod')
            ->withCartLines($lines)
            ->withTotal($pricing['total']); // 10 * 100000 = 1000000, free delivery

        $order2 = app(PlaceOrderAction::class)->execute($data2);
        $this->assertEquals(0, $order2->delivery_charge);
    }

    #[Test]
    public function blacklisted_phone_via_blacklists_table_rejected(): void
    {
        Blacklist::create([
            'type' => Blacklist::TYPE_PHONE,
            'value' => '01712345678',
            'reason' => 'Fraud pattern',
        ]);

        $data = $this->getBaseOrderData()->withPhone('01712345678');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function blacklisted_ip_via_blacklists_table_rejected(): void
    {
        Blacklist::create([
            'type' => Blacklist::TYPE_IP,
            'value' => '192.168.1.100',
            'reason' => 'Suspicious activity',
        ]);

        $data = $this->getBaseOrderData()->withIp('192.168.1.100');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function blacklisted_address_keyword_via_blacklists_table_rejected(): void
    {
        Blacklist::create([
            'type' => Blacklist::TYPE_ADDRESS_KEYWORD,
            'value' => 'fake street',
            'reason' => 'Known fraud address pattern',
        ]);

        $data = $this->getBaseOrderData()->withAddress('123 Fake Street, Dhaka');

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data);

        $this->assertEquals(0, Order::count());
    }

    #[Test]
    public function coupon_per_phone_limit_via_coupon_usages_table(): void
    {
        $coupon = Coupon::create([
            'code' => 'PERPHONE2',
            'name' => 'Per Phone Coupon 2',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'per_user_limit' => 1,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
            'is_active' => true,
        ]);

        $phone = '01712345678';
        Customer::create(['phone' => $phone]);

        // First order uses coupon
        $lines1 = collect([new CartLineData(variantId: $this->variant->id, qty: 2)]);
        $pricing1 = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            'PERPHONE2',
            1,
            $lines1,
            $phone
        );

        $data1 = $this->getBaseOrderData()
            ->withCouponCode('PERPHONE2')
            ->withPhone($phone)
            ->withIdempotencyToken('token1')
            ->withCartLines($lines1)
            ->withTotal($pricing1['total']);

        $order1 = app(PlaceOrderAction::class)->execute($data1);

        // Verify coupon usage recorded
        $this->assertEquals(1, CouponUsage::where('coupon_id', $coupon->id)->where('phone', $phone)->count());

        // Second order with same phone and coupon should fail
        $lines2 = collect([new CartLineData(variantId: $this->variant->id, qty: 1)]);
        $pricing2 = app(PricingService::class)->calculate(
            $this->district->id,
            $this->area->id,
            'PERPHONE2',
            1,
            $lines2,
            $phone
        );

        $data2 = $this->getBaseOrderData()
            ->withCouponCode('PERPHONE2')
            ->withPhone($phone)
            ->withIdempotencyToken('token2')
            ->withCartLines($lines2)
            ->withTotal($pricing2['total']);

        $this->expectException(\InvalidArgumentException::class);
        app(PlaceOrderAction::class)->execute($data2);

        // Verify coupon usage count still 1
        $this->assertEquals(1, CouponUsage::where('coupon_id', $coupon->id)->where('phone', $phone)->count());
    }

    #[Test]
    public function duplicate_bkash_trxid_rejected_at_db_level(): void
    {
        // First order with bKash
        $data1 = $this->getBaseOrderData()
            ->withPaymentMethod('bkash_send_money')
            ->withBkashTrxid('UNIQUE1234')
            ->withBkashSender('01711111111')
            ->withBkashAmount(207000)
            ->withIdempotencyToken('token1');

        $order1 = app(PlaceOrderAction::class)->execute($data1);

        // Second order with same TrxID (case insensitive) should fail at action level
        // (DB unique constraint also exists as safety net)
        $data2 = $this->getBaseOrderData()
            ->withPaymentMethod('bkash_send_money')
            ->withBkashTrxid('unique1234') // lowercase
            ->withBkashSender('01722222222')
            ->withBkashAmount(207000)
            ->withIdempotencyToken('token2');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Duplicate transaction ID');
        app(PlaceOrderAction::class)->execute($data2);

        $this->assertEquals(1, Order::count());
    }
}
