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
            $table->string('order_number')->unique(); // YYMMDD+4-digit seq
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('delivery_charge');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');
            $table->enum('status', [
                'pending',
                'approved',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'returned',
                'failed',
            ])->default('pending');
            $table->enum('payment_method', ['cod', 'bkash'])->default('cod');
            $table->enum('payment_status', ['unpaid', 'awaiting_verification', 'verified', 'refunded'])->default('unpaid');
            $table->string('bkash_trxid')->nullable()->unique();
            $table->string('bkash_sender')->nullable();
            $table->unsignedInteger('bkash_amount')->nullable();

            // Shipping info
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->foreignId('shipping_district_id')->constrained('districts')->onDelete('restrict');
            $table->foreignId('shipping_area_id')->constrained('areas')->onDelete('restrict');
            $table->text('shipping_address');
            $table->text('note')->nullable();

            // Coupon
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->unsignedInteger('discount_amount')->default(0);

            // Metadata
            $table->json('items_snapshot')->nullable(); // for historical accuracy
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('order_number');
            $table->index('bkash_trxid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
