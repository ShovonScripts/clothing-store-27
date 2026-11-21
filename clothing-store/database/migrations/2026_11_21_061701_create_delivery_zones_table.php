<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // inside_dhaka, outside_dhaka
            $table->string('name');
            $table->unsignedInteger('charge');
            $table->unsignedInteger('free_over')->nullable();
            $table->unsignedInteger('est_days_min')->default(1);
            $table->unsignedInteger('est_days_max')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
