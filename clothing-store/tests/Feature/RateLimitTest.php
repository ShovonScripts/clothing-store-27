<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SizeChart;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\DeliveryZoneSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\SizeChartSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function checkout_is_rate_limited_per_ip(): void
    {
        // Checkout uses 'checkout' key with limit 5 (per IP)
        RateLimiter::clear('checkout');

        // First 5 attempts should succeed
        for ($i = 0; $i < 5; $i++) {
            $this->assertFalse(RateLimiter::tooManyAttempts('checkout', 5), "Checkout attempt $i was rate limited unexpectedly");
            RateLimiter::hit('checkout');
        }

        // 6th attempt should be rate limited
        $this->assertTrue(RateLimiter::tooManyAttempts('checkout', 5), 'Checkout rate limit not applied on 6th attempt');
    }

    #[Test]
    public function track_order_is_rate_limited(): void
    {
        // Track order uses 'track-order:{orderNumber}:{phone}' key with limit 10 per minute
        $key = 'track-order:261125-0001:01712345678';
        RateLimiter::clear($key);

        // First 10 attempts should succeed
        for ($i = 0; $i < 10; $i++) {
            $this->assertFalse(RateLimiter::tooManyAttempts($key, 10), "Track order attempt $i was rate limited unexpectedly");
            RateLimiter::hit($key);
        }

        // 11th attempt should be rate limited
        $this->assertTrue(RateLimiter::tooManyAttempts($key, 10), 'Track order rate limit not applied on 11th attempt');
    }

    #[Test]
    public function search_suggestions_is_rate_limited(): void
    {
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(DistrictSeeder::class);
        $this->seed(DeliveryZoneSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $category = Category::where('is_active', true)->whereNull('parent_id')->first();
        $sizeChart = SizeChart::where('is_active', true)->first();
        Product::factory()->count(5)->create([
            'category_id' => $category->id,
            'size_chart_id' => $sizeChart->id,
            'is_active' => true,
        ]);

        // First 30 requests should succeed
        for ($i = 0; $i < 30; $i++) {
            $response = $this->get('/search/suggestions?q=shirt');
            $this->assertNotEquals(429, $response->status(), "Search suggestion $i was rate limited unexpectedly");
        }

        // 31st request should be rate limited (30 per minute)
        $response = $this->get('/search/suggestions?q=shirt');
        $this->assertEquals(429, $response->status(), 'Search suggestions rate limit not applied');
    }

    #[Test]
    public function login_is_rate_limited(): void
    {
        // Filament login uses Laravel's throttle:5/min default
        RateLimiter::clear('login');

        for ($i = 0; $i < 5; $i++) {
            $this->assertFalse(RateLimiter::tooManyAttempts('login', 5), "Login attempt $i was rate limited unexpectedly");
            RateLimiter::hit('login');
        }

        $this->assertTrue(RateLimiter::tooManyAttempts('login', 5), 'Login rate limit not applied');
    }
}
