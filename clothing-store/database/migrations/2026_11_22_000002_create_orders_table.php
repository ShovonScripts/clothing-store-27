<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique(); // YYMMDD+4-digit seq
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('delivery_charge');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'failed_delivery',
                'returned',
            ])->default('pending');
            $table->enum('payment_method', ['cod', 'bkash_send_money'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'awaiting_verification', 'paid', 'rejected', 'refunded'])->default('unpaid');
            $table->string('bkash_trxid', 32)->nullable()->unique();
            $table->string('bkash_sender', 11)->nullable();
            $table->unsignedInteger('bkash_amount')->nullable();

            // Shipping info (snapshot at order time)
            $table->string('shipping_name');
            $table->string('shipping_phone', 11);
            $table->string('customer_email')->nullable();
            $table->foreignId('shipping_district_id')->constrained('districts')->onDelete('restrict');
            $table->foreignId('shipping_area_id')->constrained('areas')->onDelete('restrict');
            $table->text('shipping_address');
            $table->string('delivery_zone_key', 32)->nullable();
            $table->text('note')->nullable();

            // Coupon
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->unsignedInteger('discount_amount')->default(0);

            // Approval / cancellation
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();

            // Logistics
            $table->string('courier')->nullable();
            $table->string('consignment_id')->nullable();
            $table->string('tracking_url')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedInteger('cod_amount')->default(0);

            // Metadata
            $table->json('items_snapshot')->nullable(); // historical accuracy
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('source')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('order_number');
            $table->index('payment_status');
            $table->index('shipping_phone');
            $table->index('bkash_trxid');
            $table->index('consignment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
