<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 11)->unique(); // normalized: 01XXXXXXXXX
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('default_address')->nullable();
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->string('area')->nullable();
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('delivered_orders')->default(0);
            $table->unsignedInteger('cancelled_orders')->default(0);
            $table->unsignedInteger('total_spent')->default(0);
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->unsignedInteger('pending_orders_today')->default(0);
            $table->date('last_order_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_blacklisted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
