<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Storefront\ProductFilters;
use App\Livewire\Storefront\ProductGallery;
use App\Livewire\Storefront\VariantPicker;
use App\Models\Product;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireComponentTest extends TestCase
{
    public function test_variant_picker_can_mount(): void
    {
        $product = Product::factory()->create();

        Livewire::test(VariantPicker::class, ['product' => $product])
            ->assertStatus(200)
            ->assertSee($product->getDisplayNameAttribute());
    }

    public function test_product_gallery_can_mount(): void
    {
        $product = Product::factory()->create();

        Livewire::test(ProductGallery::class, ['product' => $product])
            ->assertStatus(200);
    }

    public function test_product_filters_can_mount(): void
    {
        Livewire::test(ProductFilters::class, ['categorySlug' => ''])
            ->assertStatus(200);
    }
}
