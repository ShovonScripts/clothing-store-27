<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsServiceCacheTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SettingsService::class);
        $this->seed(SettingsSeeder::class);
        Cache::flush();
    }

    public function test_get_caches_settings(): void
    {
        // First call should hit database and cache
        $value1 = $this->service->get('store_name');
        $this->assertEquals('{BRAND}', $value1);

        // Second call should use cache
        $value2 = $this->service->get('store_name');
        $this->assertEquals('{BRAND}', $value2);

        // Verify cache has the key
        $this->assertTrue(Cache::has('store_settings'));
    }

    public function test_set_busts_cache(): void
    {
        // Prime the cache
        $this->service->get('store_name');
        $this->assertTrue(Cache::has('store_settings'));

        // Change the setting
        $this->service->set('store_name', 'New Brand', 'string', 'store');

        // Cache should be busted
        $this->assertFalse(Cache::has('store_settings'));

        // Next get should fetch new value
        $value = $this->service->get('store_name');
        $this->assertEquals('New Brand', $value);

        // Cache should be re-populated
        $this->assertTrue(Cache::has('store_settings'));
    }

    public function test_forget_busts_cache(): void
    {
        // Prime the cache
        $this->service->get('store_name');
        $this->assertTrue(Cache::has('store_settings'));

        // Add a temporary setting
        $this->service->set('temp_setting', 'temp_value', 'string', 'test');
        $this->assertEquals('temp_value', $this->service->get('temp_setting'));

        // Forget the setting
        $this->service->forget('temp_setting');

        // Cache should be busted
        $this->assertFalse(Cache::has('store_settings'));

        // Setting should be gone
        $this->assertNull($this->service->get('temp_setting'));
    }

    public function test_clear_cache(): void
    {
        // Prime the cache
        $this->service->get('store_name');
        $this->assertTrue(Cache::has('store_settings'));

        // Clear cache
        $this->service->clearCache();

        // Cache should be empty
        $this->assertFalse(Cache::has('store_settings'));

        // Next get should re-populate cache
        $value = $this->service->get('store_name');
        $this->assertEquals('{BRAND}', $value);
        $this->assertTrue(Cache::has('store_settings'));
    }

    public function test_typed_getters_use_cached_values(): void
    {
        // Prime the cache
        $this->service->getAll();

        // Test typed getters
        $this->assertEquals('{BRAND}', $this->service->storeName());
        $this->assertEquals('01XXXXXXXXX', $this->service->storePhone());
        $this->assertEquals(70, $this->service->deliveryInsideDhaka());
        $this->assertEquals(130, $this->service->deliveryOutsideDhaka());
        $this->assertEquals(24, $this->service->bkashVerifyHours());
        $this->assertTrue($this->service->codEnabled());
        $this->assertTrue($this->service->bkashEnabled());
        $this->assertEquals(5, $this->service->lowStockThreshold());
        $this->assertEquals(3, $this->service->maxPendingOrdersPerPhonePerDay());
    }

    public function test_set_with_different_types(): void
    {
        // Test int
        $this->service->set('test_int', '123', 'int', 'test');
        $this->assertEquals(123, $this->service->get('test_int'));
        $this->assertIsInt($this->service->get('test_int'));

        // Test bool
        $this->service->set('test_bool', '1', 'bool', 'test');
        $this->assertTrue($this->service->get('test_bool'));

        $this->service->set('test_bool_false', '0', 'bool', 'test');
        $this->assertFalse($this->service->get('test_bool_false'));

        // Test json
        $this->service->set('test_json', ['a' => 1, 'b' => 2], 'json', 'test');
        $this->assertEquals(['a' => 1, 'b' => 2], $this->service->get('test_json'));
    }
}
