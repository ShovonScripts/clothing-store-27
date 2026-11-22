<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('restrict');
            $table->string('product_name');
            $table->string('product_slug');
            $table->string('variant_size');
            $table->string('variant_color');
            $table->string('image')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('qty');
            $table->unsignedInteger('line_total');
            $table->timestamps();

            $table->index(['order_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
