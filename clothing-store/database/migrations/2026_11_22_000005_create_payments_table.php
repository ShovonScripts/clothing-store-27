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
            $table->enum('method', ['cod', 'bkash']);
            $table->enum('status', ['unpaid', 'awaiting_verification', 'verified', 'rejected', 'refunded'])->default('unpaid');
            $table->string('bkash_trxid')->nullable()->unique();
            $table->string('bkash_sender')->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'method']);
            $table->index('bkash_trxid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
