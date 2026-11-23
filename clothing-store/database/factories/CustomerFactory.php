<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'phone' => '017'.str_pad((string) (12345678 + $sequence), 8, '0', STR_PAD_LEFT),
            'name' => 'Test Customer '.$sequence,
            'email' => "test{$sequence}@example.com",
            'default_address' => '123 Test Street, Dhaka',
            'district_id' => 1,
            'area' => 'Dhanmondi',
            'total_orders' => 0,
            'delivered_orders' => 0,
            'cancelled_orders' => 0,
            'total_spent' => 0,
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'pending_orders_today' => 0,
            'last_order_date' => null,
            'notes' => null,
        ];
    }
}
