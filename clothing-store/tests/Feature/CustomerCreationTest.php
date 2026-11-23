<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function customer_created_in_setup(): void
    {
        $customer = Customer::firstOrCreate(
            ['phone' => '01712345678'],
            [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'default_address' => '123 Test Street, Dhaka',
                'district_id' => 1,
                'area' => 'Dhanmondi',
            ]
        );

        // Check if customer exists in DB
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'phone' => '01712345678']);

        // Check if customer is visible via query
        $found = Customer::find($customer->id);
        $this->assertNotNull($found);

        // Try to create an order with this customer
        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'TEST123456',
            'subtotal' => 100000,
            'delivery_charge' => 7000,
            'discount' => 0,
            'total' => 107000,
            'status' => OrderStatus::Pending,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '01712345678',
            'shipping_district_id' => 1,
            'shipping_area_id' => 1,
            'shipping_address' => '123 Test Street, Dhaka',
        ]);

        $this->assertEquals($customer->id, $order->customer_id);
    }
}
