<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['cod', 'bkash_send_money']);
            $table->enum('status', ['unpaid', 'awaiting_verification', 'paid', 'rejected', 'refunded'])->default('unpaid');
            $table->string('bkash_trxid', 32)->nullable()->unique(); // uppercase normalised
            $table->string('bkash_sender', 11)->nullable();
            $table->unsignedInteger('bkash_amount_sent')->nullable();
            $table->string('bkash_receiver_number', 11)->nullable();
            $table->unsignedInteger('amount')->nullable(); // expected amount
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('collected_at')->nullable(); // COD
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'method']);
            $table->index('status');
            $table->index('bkash_trxid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
