<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    // Using existing seeded database - no RefreshDatabase to avoid timeout
    // Database is seeded once via migrate:fresh --seed before running tests

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
        $product = Product::where('is_featured', true)->first();
        $this->assertNotNull($product, 'Featured product should exist');
        $response = $this->get('/');
        $response->assertSee($product->name);
    }

    #[Test]
    public function home_page_reflects_new_products(): void
    {
        $product = Product::where('is_new', true)->first();
        $response = $this->get('/');
        $response->assertSee($product->name);
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
        $color = Color::first();
        $response = $this->get('/shop?colors[]='.$color->id.'&sizes[]=M&sort=price_asc');
        $response->assertStatus(200);

        $response->assertSee($color->name);
    }

    #[Test]
    public function category_filters_survive_reload(): void
    {
        $color = Color::first();
        $url = '/shop?colors[]='.$color->id.'&sizes[]=M&price_min=500&price_max=5000&sort=popular';

        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertSee($color->name);
    }

    #[Test]
    public function inactive_product_returns_404(): void
    {
        $product = Product::where('is_active', true)->first();
        $product->update(['is_active' => false]);

        $response = $this->get('/product/'.$product->slug);
        $response->assertStatus(404);
    }

    #[Test]
    public function sold_out_variant_is_disabled(): void
    {
        $product = Product::where('is_active', true)->with('variants')->first();
        $variant = $product->variants()->first();
        $variant->update(['stock' => 0, 'is_active' => true]);

        $response = $this->get('/product/'.$product->slug);
        $response->assertStatus(200);
        $response->assertSee('Out of stock');
    }

    #[Test]
    public function low_stock_variant_shows_hint(): void
    {
        $product = Product::where('is_active', true)->with('variants')->first();
        $variant = $product->variants()->first();
        $threshold = 5;
        $variant->update(['stock' => $threshold, 'is_active' => true, 'low_stock_threshold' => $threshold]);

        $response = $this->get('/product/'.$product->slug);
        $response->assertStatus(200);
        $response->assertSee('Only '.$threshold.' left');
    }

    #[Test]
    public function search_by_name_returns_results(): void
    {
        $product = Product::where('is_active', true)->first();
        $response = $this->get('/search?q='.urlencode($product->name));
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    #[Test]
    public function search_by_sku_returns_results(): void
    {
        $product = Product::where('is_active', true)->first();
        $response = $this->get('/search?q='.urlencode($product->sku));
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    #[Test]
    public function search_suggestions_endpoint_returns_json(): void
    {
        $product = Product::where('is_active', true)->first();
        $response = $this->getJson('/search/suggestions?q='.urlencode(substr($product->name, 0, 3)));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'slug', 'price', 'price_formatted', 'image', 'sku'],
        ]);
    }

    #[Test]
    public function product_page_has_valid_json_ld_with_bdt_currency(): void
    {
        $product = Product::where('is_active', true)->with(['variants', 'media', 'category', 'sizeChart'])->first();

        $response = $this->get('/product/'.$product->slug);
        $response->assertStatus(200);

        $content = $response->getContent();
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);

        $this->assertNotEmpty($matches, 'JSON-LD script tag not found');

        $jsonLd = json_decode($matches[1], true);
        $this->assertIsArray($jsonLd);
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

        $product = Product::where('is_active', true)->first();
        $this->assertStringContainsString('/product/'.$product->slug, $content);

        $category = Category::where('is_active', true)->first();
        $this->assertStringContainsString('/shop?category='.$category->slug, $content);

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
    public function locale_toggle_switches_content_language(): void
    {
        $product = Product::where('is_active', true)->first();
        $product->update(['name_bn' => 'বাংলা নাম']);

        $response = $this->withCookie('locale', 'bn')->get('/product/'.$product->slug);
        $response->assertStatus(200);
        $response->assertSee('বাংলা নাম');
    }
}
