<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    public function test_order_status_values(): void
    {
        $this->assertEquals('pending', OrderStatus::Pending->value);
        $this->assertEquals('confirmed', OrderStatus::Confirmed->value);
        $this->assertEquals('processing', OrderStatus::Processing->value);
        $this->assertEquals('shipped', OrderStatus::Shipped->value);
        $this->assertEquals('delivered', OrderStatus::Delivered->value);
        $this->assertEquals('cancelled', OrderStatus::Cancelled->value);
        $this->assertEquals('failed_delivery', OrderStatus::FailedDelivery->value);
        $this->assertEquals('returned', OrderStatus::Returned->value);
    }

    public function test_order_status_transitions(): void
    {
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Confirmed));
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Cancelled));
        $this->assertFalse(OrderStatus::Pending->canTransitionTo(OrderStatus::Delivered));

        $this->assertTrue(OrderStatus::Confirmed->canTransitionTo(OrderStatus::Processing));
        $this->assertTrue(OrderStatus::Confirmed->canTransitionTo(OrderStatus::Shipped));
        $this->assertTrue(OrderStatus::Confirmed->canTransitionTo(OrderStatus::Cancelled));

        $this->assertTrue(OrderStatus::Processing->canTransitionTo(OrderStatus::Shipped));
        $this->assertTrue(OrderStatus::Processing->canTransitionTo(OrderStatus::Cancelled));

        $this->assertTrue(OrderStatus::Shipped->canTransitionTo(OrderStatus::Delivered));
        $this->assertTrue(OrderStatus::Shipped->canTransitionTo(OrderStatus::FailedDelivery));
        $this->assertTrue(OrderStatus::Shipped->canTransitionTo(OrderStatus::Returned));

        $this->assertTrue(OrderStatus::FailedDelivery->canTransitionTo(OrderStatus::Shipped));
        $this->assertTrue(OrderStatus::FailedDelivery->canTransitionTo(OrderStatus::Returned));
        $this->assertTrue(OrderStatus::FailedDelivery->canTransitionTo(OrderStatus::Cancelled));

        $this->assertTrue(OrderStatus::Delivered->canTransitionTo(OrderStatus::Returned));

        $this->assertFalse(OrderStatus::Cancelled->canTransitionTo(OrderStatus::Confirmed));
        $this->assertFalse(OrderStatus::Returned->canTransitionTo(OrderStatus::Confirmed));
    }

    public function test_order_status_is_terminal(): void
    {
        $this->assertTrue(OrderStatus::Delivered->isTerminal());
        $this->assertTrue(OrderStatus::Cancelled->isTerminal());
        $this->assertTrue(OrderStatus::Returned->isTerminal());
        $this->assertFalse(OrderStatus::Pending->isTerminal());
        $this->assertFalse(OrderStatus::Confirmed->isTerminal());
    }

    public function test_payment_status_values(): void
    {
        $this->assertEquals('unpaid', PaymentStatus::Unpaid->value);
        $this->assertEquals('awaiting_verification', PaymentStatus::AwaitingVerification->value);
        $this->assertEquals('paid', PaymentStatus::Paid->value);
        $this->assertEquals('rejected', PaymentStatus::Rejected->value);
        $this->assertEquals('refunded', PaymentStatus::Refunded->value);
    }

    public function test_payment_method_values(): void
    {
        $this->assertEquals('cod', PaymentMethod::Cod->value);
        $this->assertEquals('bkash_send_money', PaymentMethod::BkashSendMoney->value);
    }

    public function test_payment_method_requires_verification(): void
    {
        $this->assertFalse(PaymentMethod::Cod->requiresVerification());
        $this->assertTrue(PaymentMethod::BkashSendMoney->requiresVerification());
    }

    public function test_payment_method_raw_labels(): void
    {
        $this->assertEquals('Cash on Delivery', PaymentMethod::Cod->getRawLabel());
        $this->assertEquals('bKash Send Money', PaymentMethod::BkashSendMoney->getRawLabel());
    }
}
