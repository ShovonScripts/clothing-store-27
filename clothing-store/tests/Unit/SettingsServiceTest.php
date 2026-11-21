<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SettingsService::class);
        Cache::flush();
        $this->seed(SettingsSeeder::class);
    }

    public function test_get_existing_setting(): void
    {
        $value = $this->service->get('store_name');
        $this->assertEquals('{BRAND}', $value);
    }

    public function test_get_non_existent_returns_default(): void
    {
        $value = $this->service->get('non_existent', 'default_value');
        $this->assertEquals('default_value', $value);
    }

    public function test_get_all_returns_array(): void
    {
        $all = $this->service->getAll();
        $this->assertIsArray($all);
        $this->assertArrayHasKey('store_name', $all);
    }

    public function test_set_and_get_string(): void
    {
        $this->service->set('test_key', 'test_value', 'string', 'test');
        $this->assertEquals('test_value', $this->service->get('test_key'));
    }

    public function test_set_and_get_int(): void
    {
        $this->service->set('test_int', '123', 'int', 'test');
        $this->assertEquals(123, $this->service->get('test_int'));
    }

    public function test_set_and_get_bool(): void
    {
        $this->service->set('test_bool', '1', 'bool', 'test');
        $this->assertTrue($this->service->get('test_bool'));

        $this->service->set('test_bool_false', '0', 'bool', 'test');
        $this->assertFalse($this->service->get('test_bool_false'));
    }

    public function test_set_and_get_json(): void
    {
        $this->service->set('test_json', ['a' => 1, 'b' => 2], 'json', 'test');
        $value = $this->service->get('test_json');
        $this->assertEquals(['a' => 1, 'b' => 2], $value);
    }

    public function test_typed_getters(): void
    {
        $this->assertEquals('{BRAND}', $this->service->storeName());
        $this->assertEquals('01XXXXXXXXX', $this->service->storePhone());
        $this->assertEquals('01XXXXXXXXX', $this->service->bkashNumber());
        $this->assertEquals(24, $this->service->bkashVerifyHours());
        $this->assertTrue($this->service->codEnabled());
        $this->assertTrue($this->service->bkashEnabled());
        $this->assertEquals(70, $this->service->deliveryInsideDhaka());
        $this->assertEquals(130, $this->service->deliveryOutsideDhaka());
        $this->assertEquals(5, $this->service->lowStockThreshold());
        $this->assertEquals(3, $this->service->maxPendingOrdersPerPhonePerDay());
    }

    public function test_forget_clears_cache(): void
    {
        $this->service->set('to_forget', 'value', 'string', 'test');
        $this->assertEquals('value', $this->service->get('to_forget'));

        $this->service->forget('to_forget');
        $this->assertNull($this->service->get('to_forget'));
    }

    public function test_clear_cache(): void
    {
        $this->service->clearCache();
        $all = $this->service->getAll();
        $this->assertIsArray($all);
    }
}
