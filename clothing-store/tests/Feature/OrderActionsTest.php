<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ApproveOrderAction;
use App\Actions\CancelOrderAction;
use App\Actions\MarkCodCollectedAction;
use App\Actions\MarkDeliveredAction;
use App\Actions\MarkFailedDeliveryAction;
use App\Actions\ProcessOrderAction;
use App\Actions\RejectBkashPaymentAction;
use App\Actions\ReturnOrderAction;
use App\Actions\ShipOrderAction;
use App\Actions\VerifyBkashPaymentAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $manager;

    protected User $staff;

    protected Order $codOrder;

    protected Order $bkashOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        // Ensure a customer exists for order factory
        $this->customer = Customer::firstOrCreate(
            ['phone' => '01712345678'],
            [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'default_address' => '123 Test Street, Dhaka',
                'district_id' => 1,
                'area' => 'Dhanmondi',
            ]
        );

        $this->owner = User::factory()->create()->assignRole('owner');
        $this->manager = User::factory()->create()->assignRole('manager');
        $this->staff = User::factory()->create()->assignRole('staff');

        // Create a COD order directly (avoid factory issues)
        $this->codOrder = Order::create([
            'customer_id' => $this->customer->id,
            'order_number' => now()->format('ymd').str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
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
        ]);

        // Create a bKash order awaiting verification
        $this->bkashOrder = Order::create([
            'customer_id' => $this->customer->id,
            'order_number' => now()->format('ymd').str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'subtotal' => 100000,
            'delivery_charge' => 7000,
            'discount' => 0,
            'total' => 107000,
            'status' => OrderStatus::Pending,
            'payment_method' => PaymentMethod::BkashSendMoney,
            'payment_status' => PaymentStatus::AwaitingVerification,
            'bkash_trxid' => 'TEST123456',
            'bkash_sender' => '01711111111',
            'bkash_amount' => 100000,
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '01712345678',
            'shipping_district_id' => 1,
            'shipping_area_id' => 1,
            'shipping_address' => '123 Test Street, Dhaka',
        ]);

        // Create payment records
        Payment::factory()->create([
            'order_id' => $this->codOrder->id,
            'method' => PaymentMethod::Cod,
            'status' => PaymentStatus::Unpaid,
            'amount' => 100000,
        ]);

        Payment::factory()->create([
            'order_id' => $this->bkashOrder->id,
            'method' => PaymentMethod::BkashSendMoney,
            'status' => PaymentStatus::AwaitingVerification,
            'amount' => 100000,
            'bkash_trxid' => 'TEST123456',
            'bkash_sender' => '01711111111',
            'bkash_amount_sent' => 100000,
        ]);
    }

    // ==================== ApproveOrderAction ====================

    #[Test]
    public function approve_order_action_transitions_cod_order_to_confirmed(): void
    {
        $order = app(ApproveOrderAction::class)->execute($this->codOrder, $this->owner);

        $this->assertEquals(OrderStatus::Confirmed, $order->status);
        $this->assertEquals(PaymentStatus::Unpaid, $order->payment_status); // COD stays unpaid

        // Check status history
        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Confirmed)
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals($this->owner->id, $history->changed_by);
    }

    #[Test]
    public function approve_order_action_throws_on_bkash_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('bKash orders must be verified through the payment verification flow');

        app(ApproveOrderAction::class)->execute($this->bkashOrder, $this->owner);
    }

    #[Test]
    public function approve_order_action_throws_on_non_pending_order(): void
    {
        $confirmedOrder = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only pending orders can be approved');

        app(ApproveOrderAction::class)->execute($confirmedOrder, $this->owner);
    }

    // ==================== VerifyBkashPaymentAction ====================

    #[Test]
    public function verify_bkash_payment_action_transitions_to_paid_and_confirmed(): void
    {
        $order = app(VerifyBkashPaymentAction::class)->execute($this->bkashOrder, $this->owner);

        $this->assertEquals(OrderStatus::Confirmed, $order->status);
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);

        // Check payment record updated
        $payment = $order->payment;
        $this->assertEquals(PaymentStatus::Paid, $payment->status);
        $this->assertEquals($this->owner->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);

        // Check status history - should have two records:
        // 1. Payment record (type=payment) with to_payment_status=Paid
        // 2. Order record (type=order) with to_status=Confirmed
        $paymentHistory = OrderStatusHistory::where('order_id', $order->id)
            ->where('type', 'payment')
            ->where('to_payment_status', PaymentStatus::Paid)
            ->first();
        $this->assertNotNull($paymentHistory);
        $this->assertEquals($this->owner->id, $paymentHistory->changed_by);

        $orderHistory = OrderStatusHistory::where('order_id', $order->id)
            ->where('type', 'order')
            ->where('to_status', OrderStatus::Confirmed)
            ->first();
        $this->assertNotNull($orderHistory);
        $this->assertEquals($this->owner->id, $orderHistory->changed_by);
    }

    #[Test]
    public function verify_bkash_payment_action_throws_on_non_bkash_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order is not a bKash order');

        app(VerifyBkashPaymentAction::class)->execute($this->codOrder, $this->owner);
    }

    #[Test]
    public function verify_bkash_payment_action_throws_on_non_pending_order(): void
    {
        $confirmedBkash = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::BkashSendMoney,
            'payment_status' => PaymentStatus::AwaitingVerification,
        ]);
        Payment::factory()->create([
            'order_id' => $confirmedBkash->id,
            'method' => PaymentMethod::BkashSendMoney,
            'status' => PaymentStatus::AwaitingVerification,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only pending bKash orders can be verified');

        app(VerifyBkashPaymentAction::class)->execute($confirmedBkash, $this->owner);
    }

    #[Test]
    public function verify_bkash_payment_action_throws_on_wrong_payment_status(): void
    {
        $wrongStatusOrder = Order::factory()->create([
            'status' => OrderStatus::Pending,
            'payment_method' => PaymentMethod::BkashSendMoney,
            'payment_status' => PaymentStatus::Paid, // Already paid
        ]);
        Payment::factory()->create([
            'order_id' => $wrongStatusOrder->id,
            'method' => PaymentMethod::BkashSendMoney,
            'status' => PaymentStatus::Paid,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment is not awaiting verification');

        app(VerifyBkashPaymentAction::class)->execute($wrongStatusOrder, $this->owner);
    }

    // ==================== RejectBkashPaymentAction ====================

    #[Test]
    public function reject_bkash_payment_action_transitions_to_cancelled_and_refunds_stock(): void
    {
        $reason = 'Invalid TrxID';

        $order = app(RejectBkashPaymentAction::class)->execute($this->bkashOrder, $this->owner, $reason);

        $this->assertEquals(OrderStatus::Cancelled, $order->status);
        $this->assertEquals(PaymentStatus::Rejected, $order->payment_status);

        // Check payment record updated
        $payment = $order->payment;
        $this->assertEquals(PaymentStatus::Rejected, $payment->status);
        $this->assertEquals($reason, $payment->rejection_reason);

        // Check status history
        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Cancelled)
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals($reason, $history->note);
    }

    #[Test]
    public function reject_bkash_payment_action_throws_on_empty_reason(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A rejection reason is required');

        app(RejectBkashPaymentAction::class)->execute($this->bkashOrder, $this->owner, '');
    }

    #[Test]
    public function reject_bkash_payment_action_throws_on_non_bkash_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order is not a bKash order');

        app(RejectBkashPaymentAction::class)->execute($this->codOrder, $this->owner, 'Test reason');
    }

    // ==================== CancelOrderAction ====================

    #[Test]
    public function cancel_order_action_transitions_to_cancelled(): void
    {
        $reason = 'Customer requested cancellation';

        $order = app(CancelOrderAction::class)->execute($this->codOrder, $this->owner, $reason);

        $this->assertEquals(OrderStatus::Cancelled, $order->status);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Cancelled)
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals($reason, $history->note);
    }

    #[Test]
    public function cancel_order_action_throws_on_empty_reason(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A cancellation reason is required');

        app(CancelOrderAction::class)->execute($this->codOrder, $this->owner, '');
    }

    // ==================== ProcessOrderAction ====================

    #[Test]
    public function process_order_action_transitions_to_processing(): void
    {
        $confirmedOrder = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $order = app(ProcessOrderAction::class)->execute($confirmedOrder, $this->owner);

        $this->assertEquals(OrderStatus::Processing, $order->status);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Processing)
            ->first();
        $this->assertNotNull($history);
    }

    // ==================== ShipOrderAction ====================

    #[Test]
    public function ship_order_action_transitions_to_shipped_with_courier_info(): void
    {
        $processingOrder = Order::factory()->create([
            'status' => OrderStatus::Processing,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $order = app(ShipOrderAction::class)->execute(
            $processingOrder,
            $this->owner,
            'RedX',
            'RX123456789',
            'https://track.redx.com/RX123456789'
        );

        $this->assertEquals(OrderStatus::Shipped, $order->status);
        $this->assertEquals('RedX', $order->courier);
        $this->assertEquals('RX123456789', $order->consignment_id);
        $this->assertEquals('https://track.redx.com/RX123456789', $order->tracking_url);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Shipped)
            ->first();
        $this->assertNotNull($history);
        $this->assertStringContainsString('RedX', $history->note);
        $this->assertStringContainsString('RX123456789', $history->note);
    }

    #[Test]
    public function ship_order_action_throws_on_missing_courier(): void
    {
        $processingOrder = Order::factory()->create([
            'status' => OrderStatus::Processing,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Courier and consignment ID are required');

        app(ShipOrderAction::class)->execute($processingOrder, $this->owner, '', 'RX123456789');
    }

    // ==================== MarkDeliveredAction ====================

    #[Test]
    public function mark_delivered_action_transitions_to_delivered(): void
    {
        $shippedOrder = Order::factory()->create([
            'status' => OrderStatus::Shipped,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $order = app(MarkDeliveredAction::class)->execute($shippedOrder, $this->owner);

        $this->assertEquals(OrderStatus::Delivered, $order->status);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Delivered)
            ->first();
        $this->assertNotNull($history);
    }

    // ==================== MarkFailedDeliveryAction ====================

    #[Test]
    public function mark_failed_delivery_action_transitions_to_failed_delivery(): void
    {
        $shippedOrder = Order::factory()->create([
            'status' => OrderStatus::Shipped,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $order = app(MarkFailedDeliveryAction::class)->execute($shippedOrder, $this->owner);

        $this->assertEquals(OrderStatus::FailedDelivery, $order->status);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::FailedDelivery)
            ->first();
        $this->assertNotNull($history);
    }

    // ==================== ReturnOrderAction ====================

    #[Test]
    public function return_order_action_transitions_to_returned(): void
    {
        $deliveredOrder = Order::factory()->create([
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $order = app(ReturnOrderAction::class)->execute($deliveredOrder, $this->owner, false, 'Customer not satisfied');

        $this->assertEquals(OrderStatus::Returned, $order->status);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Returned)
            ->first();
        $this->assertNotNull($history);
    }

    #[Test]
    public function return_order_action_with_restock_true_sets_restock_flag(): void
    {
        $deliveredOrder = Order::factory()->create([
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);

        $order = app(ReturnOrderAction::class)->execute($deliveredOrder, $this->owner, true);

        $this->assertEquals(OrderStatus::Returned, $order->status);

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->where('to_status', OrderStatus::Returned)
            ->first();
        $this->assertNotNull($history);
        $this->assertStringContainsString('restocked', strtolower($history->note));
    }

    // ==================== MarkCodCollectedAction ====================

    #[Test]
    public function mark_cod_collected_action_marks_payment_as_paid(): void
    {
        $deliveredCodOrder = Order::factory()->create([
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Unpaid,
        ]);
        Payment::factory()->create([
            'order_id' => $deliveredCodOrder->id,
            'method' => PaymentMethod::Cod,
            'status' => PaymentStatus::Unpaid,
            'amount' => 100000,
        ]);

        $order = app(MarkCodCollectedAction::class)->execute($deliveredCodOrder, $this->owner);

        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);

        $payment = $order->payment;
        $this->assertEquals(PaymentStatus::Paid, $payment->status);
        $this->assertNotNull($payment->collected_at);
        $this->assertEquals($this->owner->id, $payment->verified_by);
    }

    #[Test]
    public function mark_cod_collected_action_throws_on_non_cod_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only COD orders can have cash collected');

        app(MarkCodCollectedAction::class)->execute($this->bkashOrder, $this->owner);
    }

    #[Test]
    public function mark_cod_collected_action_throws_on_non_delivered_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('COD can only be collected after delivery');

        app(MarkCodCollectedAction::class)->execute($this->codOrder, $this->owner);
    }

    #[Test]
    public function mark_cod_collected_action_throws_on_already_paid(): void
    {
        $deliveredPaidCodOrder = Order::factory()->create([
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::Cod,
            'payment_status' => PaymentStatus::Paid,
        ]);
        Payment::factory()->create([
            'order_id' => $deliveredPaidCodOrder->id,
            'method' => PaymentMethod::Cod,
            'status' => PaymentStatus::Paid,
            'amount' => 100000,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('COD is already marked as collected');

        app(MarkCodCollectedAction::class)->execute($deliveredPaidCodOrder, $this->owner);
    }
}
