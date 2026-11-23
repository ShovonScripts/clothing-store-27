<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => null,
            'method' => PaymentMethod::Cod,
            'status' => PaymentStatus::Unpaid,
            'bkash_trxid' => null,
            'bkash_sender' => null,
            'bkash_amount_sent' => null,
            'bkash_receiver_number' => null,
            'amount' => 100000,
            'verified_by' => null,
            'verified_at' => null,
            'rejection_reason' => null,
            'collected_at' => null,
            'meta' => null,
        ];
    }

    public function bkash(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => PaymentMethod::BkashSendMoney,
            'status' => PaymentStatus::AwaitingVerification,
            'bkash_trxid' => 'TEST'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'bkash_sender' => '01711111111',
            'bkash_amount_sent' => $attributes['amount'] ?? 100000,
        ]);
    }

    public function cod(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => PaymentMethod::Cod,
            'status' => PaymentStatus::Unpaid,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Paid,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Rejected,
        ]);
    }
}
