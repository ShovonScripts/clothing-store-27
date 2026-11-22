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
            $table->string('phone')->unique(); // normalized: 8801XXXXXXXXX
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->unsignedInteger('pending_orders_today')->default(0);
            $table->date('last_order_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('is_blacklisted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
