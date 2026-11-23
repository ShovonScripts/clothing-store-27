<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\PlaceOrderAction;
use App\Data\CartLineData;
use App\Data\CheckoutData;
use App\Models\Area;
use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
use App\Services\PricingService;
use App\Support\PhoneNormalizer;
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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DebugBlacklistTest2 extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function debug_blacklist(): void
    {
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(AreaSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        // Create blacklisted customer
        $blacklistedPhone = '8801712345678';
        $customer = Customer::create([
            'phone' => $blacklistedPhone,
            'is_blacklisted' => true,
            'blacklist_reason' => 'Fraud',
        ]);

        echo "Created customer with phone: {$customer->phone}, blacklisted: {$customer->is_blacklisted}\n";
        $normCustomer = PhoneNormalizer::normalize($customer->phone);
        $normInput = PhoneNormalizer::normalize('01712345678');
        echo "Customer phone normalized: {$normCustomer}\n";
        echo "Input phone normalized: {$normInput}\n";
        echo 'Match: '.($normCustomer === $normInput ? 'YES' : 'NO')."\n";
        echo "Customer phone raw: '{$customer->phone}'\n";
        echo "Input phone raw: '01712345678'\n";

        // Test the blacklist check directly
        $customer2 = Customer::firstOrCreate(
            ['phone' => PhoneNormalizer::normalize('01712345678')],
            ['name' => 'Test', 'email' => 'test@example.com']
        );

        echo "Found customer: {$customer2->phone}, blacklisted: {$customer2->is_blacklisted}\n";

        if ($customer2->is_blacklisted) {
            echo "BLACKLISTED!\n";
        } else {
            echo "NOT BLACKLISTED\n";
        }

        // Test the actual PlaceOrderAction
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(AreaSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('is_active', true)->whereNull('parent_id')->first();
        $sizeChart = SizeChart::where('is_active', true)->first();
        $color = Color::where('is_active', true)->first();

        $product = Product::factory()->create([
            'category_id' => Category::where('is_active', true)->whereNull('parent_id')->first()->id,
            'size_chart_id' => SizeChart::where('is_active', true)->first()->id,
            'is_active' => true,
            'price' => 100000,
        ]);

        $variant = ProductVariant::factory()->forProduct($product)->create([
            'size' => 'M',
            'color_id' => Color::where('is_active', true)->first()->id,
            'stock' => 10,
            'is_active' => true,
        ]);

        $district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
        $area = Area::where('is_active', true)->where('district_id', $district->id)->first();

        $lines = collect([new CartLineData(variantId: $variant->id, qty: 2)]);

        $pricing = app(PricingService::class)->calculate(
            $district->id,
            $area->id,
            '',
            1,
            collect([new CartLineData(variantId: $variant->id, qty: 2)])
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
        ], collect([new CartLineData(variantId: $variant->id, qty: 2)]), '127.0.0.1');

        echo "Data phone: {$data->phone}\n";
        echo "Data lines count: {$data->lines->count()}\n";

        try {
            $order = app(PlaceOrderAction::class)->execute($data);
            echo "Order created: {$order->order_number}\n";
            echo 'Order count: '.Order::count()."\n";
        } catch (\Exception $e) {
            echo 'Exception: '.$e->getMessage()."\n";
        }
    }
}
