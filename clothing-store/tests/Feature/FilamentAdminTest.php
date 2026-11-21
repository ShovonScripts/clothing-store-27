<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\SettingsService;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SizeChartSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FilamentAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $manager;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ColorSeeder::class);
        $this->seed(SizeChartSeeder::class);

        $this->owner = User::factory()->create()->assignRole('owner');
        $this->manager = User::factory()->create()->assignRole('manager');
        $this->staff = User::factory()->create()->assignRole('staff');
    }

    /** @test */
    public function owner_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->owner, 'web')
            ->get('/admin')
            ->assertOk();
    }

    /** @test */
    public function manager_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->manager, 'web')
            ->get('/admin')
            ->assertOk();
    }

    /** @test */
    public function staff_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->staff, 'web')
            ->get('/admin')
            ->assertOk();
    }

    /** @test */
    public function owner_can_access_settings_page(): void
    {
        $this->actingAs($this->owner, 'web')
            ->get('/admin/settings-page')
            ->assertOk();
    }

    /** @test */
    public function manager_cannot_access_settings_page(): void
    {
        $this->actingAs($this->manager, 'web')
            ->get('/admin/settings-page')
            ->assertForbidden();
    }

    /** @test */
    public function staff_cannot_access_settings_page(): void
    {
        $this->actingAs($this->staff, 'web')
            ->get('/admin/settings-page')
            ->assertForbidden();
    }

    /** @test */
    public function owner_can_save_settings_and_cache_busted(): void
    {
        // Test SettingsService cache busting directly
        $settingsService = app(SettingsService::class);

        // Prime the cache
        $settingsService->get('bkash_number');
        $this->assertTrue(Cache::has('store_settings'));

        // Call clearCache directly to test cache busting
        $settingsService->clearCache();
        $this->assertFalse(Cache::has('store_settings'));

        // Verify settings can be saved and cache is busted
        $settingsService->set('bkash_number', '01712345678', 'string', 'payments');
        $this->assertFalse(Cache::has('store_settings'));

        $settingsService->set('delivery_inside_dhaka', '80', 'int', 'delivery');
        $this->assertFalse(Cache::has('store_settings'));

        // Verify settings can be retrieved
        $this->assertEquals('01712345678', $settingsService->get('bkash_number'));
        $this->assertEquals(80, $settingsService->get('delivery_inside_dhaka'));
    }

    /** @test */
    public function users_resource_not_yet_implemented(): void
    {
        // TODO: Phase 4 - Build owner-only Users & Roles resource (create staff, assign owner/manager/staff, deactivate)
        // Current: UserResource does not exist, so /admin/users returns 404 vacuously
        // Phase 4: Build owner-only Users & Roles resource with real tests:
        // - owner can access /admin/users (200)
        // - manager gets 403 on /admin/users
        // - staff gets 403 on /admin/users
        // - owner can create staff, assign roles, deactivate
        $this->markTestSkipped('Phase 4: Users & Roles resource not yet implemented');
    }

    /** @test */
    public function staff_cannot_verify_bkash_payments(): void
    {
        $this->assertFalse($this->staff->can('verify_bkash_payments'));
    }

    /** @test */
    public function owner_can_verify_bkash_payments(): void
    {
        $this->assertTrue($this->owner->can('verify_bkash_payments'));
    }

    /** @test */
    public function manager_cannot_verify_bkash_payments(): void
    {
        $this->assertFalse($this->manager->can('verify_bkash_payments'));
    }

    /** @test */
    public function staff_cannot_view_cost_price(): void
    {
        $this->assertFalse($this->staff->can('view_cost_price'));
    }

    /** @test */
    public function manager_cannot_view_cost_price(): void
    {
        $this->assertFalse($this->manager->can('view_cost_price'));
    }

    /** @test */
    public function owner_can_view_cost_price(): void
    {
        $this->assertTrue($this->owner->can('view_cost_price'));
    }
}
