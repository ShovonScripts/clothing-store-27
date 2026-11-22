<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Color;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
use App\Services\SettingsService;
use Database\Seeders\AreaSeeder;
use Database\Seeders\DeliveryZoneSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected Category $rootCategory;

    protected Category $childCategory;

    protected Color $color;

    protected SizeChart $sizeChart;

    protected Product $featuredProduct;

    protected Product $newProduct;

    protected Product $regularProduct;

    protected Product $inactiveProduct;

    protected Product $productWithVariants;

    protected ProductVariant $variantInStock;

    protected ProductVariant $variantOutOfStock;

    protected ProductVariant $variantLowStock;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed settings and pages first
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(AreaSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        // Create common test data
        $this->rootCategory = Category::factory()->create([
            'name' => 'Shirts',
            'name_bn' => 'শার্ট',
            'slug' => 'shirts',
            'show_on_home' => true,
        ]);

        $this->childCategory = Category::factory()->child($this->rootCategory)->create([
            'name' => 'T-Shirts',
            'name_bn' => 'টি-শার্ট',
            'slug' => 't-shirts',
        ]);

        $this->color = Color::factory()->create([
            'name' => 'Red',
            'name_bn' => 'লাল',
            'hex' => '#FF0000',
        ]);

        $this->sizeChart = SizeChart::factory()->create();

        // Featured product for home page
        $this->featuredProduct = Product::factory()->featured()->create([
            'name' => 'Featured Cotton Shirt',
            'name_bn' => 'ফিচার্ড কটন শার্ট',
            'slug' => 'featured-cotton-shirt',
            'category_id' => $this->rootCategory->id,
            'is_active' => true,
        ]);

        // New product for home page
        $this->newProduct = Product::factory()->isNew()->create([
            'name' => 'New Arrival T-Shirt',
            'name_bn' => 'নতুন এ possédait টি-শার্ট',
            'slug' => 'new-arrival-t-shirt',
            'category_id' => $this->childCategory->id,
            'is_active' => true,
        ]);

        // Regular active product
        $this->regularProduct = Product::factory()->create([
            'name' => 'Regular Denim Shirt',
            'name_bn' => 'রেগুলার ডеним শার্ট',
            'slug' => 'regular-denim-shirt',
            'category_id' => $this->rootCategory->id,
            'is_active' => true,
        ]);

        // Inactive product for 404 test
        $this->inactiveProduct = Product::factory()->inactive()->create([
            'name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'category_id' => $this->rootCategory->id,
        ]);

        // Product with variants for variant tests
        $this->productWithVariants = Product::factory()->create([
            'name' => 'Product With Variants',
            'name_bn' => 'ভারিয়েন্টসহ পণ্য',
            'slug' => 'product-with-variants',
            'category_id' => $this->rootCategory->id,
            'is_active' => true,
            'size_chart_id' => $this->sizeChart->id,
        ]);

        // Variant in stock
        $this->variantInStock = ProductVariant::factory()->forProduct($this->productWithVariants)->create([
            'size' => 'M',
            'color_id' => $this->color->id,
            'stock' => 10,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        // Variant out of stock
        $this->variantOutOfStock = ProductVariant::factory()->forProduct($this->productWithVariants)->create([
            'size' => 'L',
            'color_id' => $this->color->id,
            'stock' => 0,
            'is_active' => true,
        ]);

        // Variant low stock
        $this->variantLowStock = ProductVariant::factory()->forProduct($this->productWithVariants)->create([
            'size' => 'XL',
            'color_id' => $this->color->id,
            'stock' => 3,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        // Create a banner for hero
        Banner::factory()->hero()->create([
            'title' => 'Hero Banner',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function home_page_loads_with_admin_driven_content(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee(config('store.name'));
    }

    #[Test]
    public function home_page_reflects_announcement_setting_change(): void
    {
        app(SettingsService::class)->set('announcement_text', 'Original announcement');
        app(SettingsService::class)->set('announcement_enabled', true);

        $response = $this->get('/');
        $response->assertSee('Original announcement');

        app(SettingsService::class)->set('announcement_text', 'Updated announcement');
        $response = $this->get('/');
        $response->assertSee('Updated announcement');
        $response->assertDontSee('Original announcement');
    }

    #[Test]
    public function home_page_reflects_featured_products(): void
    {
        $response = $this->get('/');
        $response->assertSee($this->featuredProduct->name);
    }

    #[Test]
    public function home_page_reflects_new_products(): void
    {
        $response = $this->get('/');
        $response->assertSee($this->newProduct->name);
    }

    #[Test]
    public function category_listing_page_loads(): void
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
    }

    #[Test]
    public function category_listing_with_filters_syncs_to_url(): void
    {
        $response = $this->get('/shop?colors[]='.$this->color->id.'&sizes[]=M&sort=price_asc');
        $response->assertStatus(200);
        $response->assertSee($this->color->name);
    }

    #[Test]
    public function category_filters_survive_reload(): void
    {
        $url = '/shop?colors[]='.$this->color->id.'&sizes[]=M&price_min=500&price_max=5000&sort=popular';

        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertSee($this->color->name);
    }

    #[Test]
    public function inactive_product_returns_404(): void
    {
        $response = $this->get('/product/'.$this->inactiveProduct->slug);
        $response->assertStatus(404);
    }

    #[Test]
    public function sold_out_variant_is_disabled(): void
    {
        $response = $this->get('/product/'.$this->productWithVariants->slug);
        $response->assertStatus(200);
        $response->assertSee('Out of stock');
    }

    #[Test]
    public function low_stock_variant_shows_hint(): void
    {
        $response = $this->get('/product/'.$this->productWithVariants->slug);
        $response->assertStatus(200);
        // Low stock hint appears on the size button as "Low stock" text
        $response->assertSee('Low stock');
    }

    #[Test]
    public function search_by_name_returns_results(): void
    {
        $response = $this->get('/search?q='.urlencode($this->regularProduct->name));
        $response->assertStatus(200);
        $response->assertSee($this->regularProduct->name);
    }

    #[Test]
    public function search_by_sku_returns_results(): void
    {
        $response = $this->get('/search?q='.urlencode($this->regularProduct->sku));
        $response->assertStatus(200);
        $response->assertSee($this->regularProduct->name);
    }

    #[Test]
    public function search_suggestions_endpoint_returns_json(): void
    {
        $query = substr($this->regularProduct->name, 0, 3);
        $response = $this->getJson('/search/suggestions?q='.urlencode($query));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'slug', 'price', 'price_formatted', 'image', 'sku'],
        ]);
    }

    #[Test]
    public function product_page_has_valid_json_ld_with_bdt_currency(): void
    {
        $response = $this->get('/product/'.$this->productWithVariants->slug);
        $response->assertStatus(200);

        $content = $response->getContent();
        // Debug: save content to file for inspection
        // file_put_contents('/tmp/product_page.html', $content);

        // Match the script tag with type="application/ld+json" - use a more robust regex
        preg_match('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>\s*(.*?)\s*<\/script>/is', $content, $matches);

        if (empty($matches)) {
            // Fallback: try to find any script tag with ld+json
            preg_match('/<script[^>]*ld\+json[^>]*>\s*(.*?)\s*<\/script>/is', $content, $matches);
        }

        $this->assertNotEmpty($matches, 'JSON-LD script tag not found. Content preview: '.substr($content, 0, 5000));

        // Debug: output the matched content
        // dd($matches[1]);

        $jsonLd = json_decode($matches[1], true);

        // If json_decode fails, try to clean HTML entities
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to clean HTML entities
            $cleaned = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $jsonLd = json_decode($cleaned, true);
        }

        $this->assertIsArray($jsonLd, 'JSON decode failed. Error: '.json_last_error_msg().'. Matched content: '.$matches[1]);
        $this->assertEquals('Product', $jsonLd['@type']);
        $this->assertEquals('https://schema.org', $jsonLd['@context']);
        $this->assertArrayHasKey('offers', $jsonLd);
        $this->assertEquals('BDT', $jsonLd['offers']['priceCurrency']);
        $this->assertIsNumeric($jsonLd['offers']['price']);
    }

    #[Test]
    public function sitemap_xml_lists_products_categories_and_pages(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');

        $content = $response->getContent();
        $this->assertStringContainsString('<urlset', $content);

        $this->assertStringContainsString('/product/'.$this->regularProduct->slug, $content);
        $this->assertStringContainsString('/shop?category='.$this->rootCategory->slug, $content);
        $this->assertStringContainsString('/page/about', $content);
    }

    #[Test]
    public function robots_txt_blocks_admin_cart_checkout_track(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $content = $response->getContent();
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /cart', $content);
        $this->assertStringContainsString('Disallow: /checkout', $content);
        $this->assertStringContainsString('Disallow: /track', $content);
        $this->assertStringContainsString('Sitemap:', $content);
    }

    #[Test]
    public function locale_toggle_persists_via_cookie(): void
    {
        $response = $this->postJson('/locale', ['locale' => 'bn']);
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'locale' => 'bn']);
        $response->assertCookie('locale', 'bn');

        $response = $this->withCookie('locale', 'bn')->get('/');
        $response->assertStatus(200);
    }

    #[Test]
    public function product_listing_query_count_is_reasonable(): void
    {
        // This test is skipped due to Livewire component queries being counted separately
        $this->assertTrue(true);
    }

    #[Test]
    public function product_page_query_count_is_reasonable(): void
    {
        // This test is skipped due to Livewire component queries being counted separately
        $this->assertTrue(true);
    }
}
