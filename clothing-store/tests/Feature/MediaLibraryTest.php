<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\SizeChart;
use Database\Seeders\CategorySeeder;
use Database\Seeders\SizeChartSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileUnacceptableForCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_webp_support_enabled(): void
    {
        $gdInfo = gd_info();
        $this->assertTrue($gdInfo['WebP Support'] ?? false, 'GD WebP support is required for image conversions');
    }

    public function test_product_can_attach_image_and_generate_conversions(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('slug', 't-shirts')->first();
        $color = Color::where('name', 'Black')->first();
        $sizeChart = SizeChart::where('name', 'T-Shirt / Polo (inches)')->first();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test T-Shirt',
            'name_bn' => 'টেস্ট টি-শার্ট',
            'slug' => 'test-t-shirt-'.uniqid(),
            'price' => 1000,
            'description' => 'Test product',
            'fabric' => '100% Cotton',
            'fit' => 'regular',
            'care' => 'Machine wash',
            'size_chart_id' => SizeChart::where('name', 'T-Shirt / Polo (inches)')->first()->id,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Storage::fake('public');

        // Create a test image
        $image = UploadedFile::fake()->image('test-product.jpg', 800, 1000);

        // Attach to product's gallery collection
        $media = $product->addMedia($image)
            ->toMediaCollection('gallery');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals('gallery', $media->collection_name);

        // Check that conversions exist
        $conversions = ['thumb', 'card', 'large'];
        foreach ($conversions as $conversion) {
            $conversionPath = $media->getPath($conversion);
            $this->assertFileExists($conversionPath, "Conversion '{$conversion}' should exist at {$conversionPath}");

            // Verify it's a WebP file
            $mimeType = mime_content_type($conversionPath);
            $this->assertEquals('image/webp', $mimeType, "Conversion '{$conversion}' should be WebP format");
        }

        // Check conversion dimensions
        $thumbInfo = getimagesize($media->getPath('thumb'));
        // Thumb is 320x400 due to aspect ratio preservation (800x1000 -> 320x400)
        $this->assertEquals(320, $thumbInfo[0], 'Thumb width should be 320px');
        $this->assertEquals(400, $thumbInfo[1], 'Thumb should be 400px high');

        $cardInfo = getimagesize($media->getPath('card'));
        $this->assertEquals(480, $cardInfo[0], 'Card should be 480px wide');
        $this->assertEquals(600, $cardInfo[1], 'Card should be 600px high');

        $largeInfo = getimagesize($media->getPath('large'));
        $this->assertEquals(960, $largeInfo[0], 'Large should be 960px wide');
        $this->assertEquals(1200, $largeInfo[1], 'Large should be 1200px high');
    }

    public function test_product_rejects_non_image_files(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('slug', 't-shirts')->first();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test T-Shirt',
            'name_bn' => 'টেস্ট টি-শার্ট',
            'slug' => 'test-t-shirt-'.uniqid(),
            'price' => 1000,
            'description' => 'Test product',
            'fabric' => '100% Cotton',
            'fit' => 'regular',
            'care' => 'Machine wash',
            'size_chart_id' => SizeChart::where('name', 'T-Shirt / Polo (inches)')->first()->id,
            'is_active' => true,
            'published_at' => now(),
        ]);

        // Try to upload a non-image file (should be rejected by acceptsMimeTypes)
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->expectException(FileUnacceptableForCollection::class);

        $product->addMedia($file)
            ->toMediaCollection('gallery');
    }

    public function test_product_rejects_large_files(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('slug', 't-shirts')->first();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test T-Shirt',
            'name_bn' => 'টেস্ট টি-শার্ট',
            'slug' => 'test-t-shirt-'.uniqid(),
            'price' => 1000,
            'description' => 'Test product',
            'fabric' => '100% Cotton',
            'fit' => 'regular',
            'care' => 'Machine wash',
            'size_chart_id' => SizeChart::where('name', 'T-Shirt / Polo (inches)')->first()->id,
            'is_active' => true,
            'published_at' => now(),
        ]);

        // Create a file larger than 5MB
        $largeFile = UploadedFile::fake()->create('large.jpg', 6000, 'image/jpeg');

        $this->expectException(FileUnacceptableForCollection::class);

        $product->addMedia($largeFile)
            ->toMediaCollection('gallery');
    }

    public function test_multiple_images_can_be_attached(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('slug', 't-shirts')->first();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test T-Shirt',
            'name_bn' => 'টেস্ট টি-শার্ট',
            'slug' => 'test-t-shirt-'.uniqid(),
            'price' => 1000,
            'description' => 'Test product',
            'fabric' => '100% Cotton',
            'fit' => 'regular',
            'care' => 'Machine wash',
            'size_chart_id' => SizeChart::where('name', 'T-Shirt / Polo (inches)')->first()->id,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Storage::fake('public');

        // Attach multiple images
        for ($i = 1; $i <= 3; $i++) {
            $image = UploadedFile::fake()->image("product-{$i}.jpg", 800, 1000);
            $product->addMedia($image)
                ->toMediaCollection('gallery');
        }

        $mediaCount = $product->getMedia('gallery')->count();
        $this->assertEquals(3, $mediaCount);
    }
}
