<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Use provided customer_id or fall back to first customer
        // Access the factory's attributes directly
        $attributes = $this->attributes ?? [];
        $customerId = $attributes['customer_id'] ?? null;

        if (! $customerId && $customer = Customer::first()) {
            $customerId = $customer->id;
        } elseif (! $customerId) {
            $customer = Customer::create([
                'phone' => '01712345678',
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'default_address' => '123 Test Street, Dhaka',
                'district_id' => 1,
                'area' => 'Dhanmondi',
            ]);
            $customerId = $customer->id;
        }

        return [
            'order_number' => now()->format('ymd').str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => $customerId,
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
            'note' => null,
            'coupon_id' => null,
            'discount_amount' => 0,
            'cod_amount' => 107000,
            'items_snapshot' => [],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'source' => 'web',
            'admin_note' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Confirmed,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Processing,
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Shipped,
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Delivered,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Cancelled,
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Returned,
        ]);
    }

    public function failedDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::FailedDelivery,
        ]);
    }

    public function bkash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => PaymentMethod::BkashSendMoney,
            'payment_status' => PaymentStatus::AwaitingVerification,
            'bkash_trxid' => 'TEST'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'bkash_sender' => '01711111111',
            'bkash_amount' => $attributes['total'] ?? 107000,
            'cod_amount' => 0,
        ]);
    }

    public function cod(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
            'cod_amount' => $attributes['total'] ?? 107000,
        ]);
    }
}
