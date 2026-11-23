<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    protected Role $ownerRole;

    protected Role $managerRole;

    protected Role $staffRole;

    protected User $owner;

    protected User $manager;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->ownerRole = Role::where('name', 'owner')->first();
        $this->managerRole = Role::where('name', 'manager')->first();
        $this->staffRole = Role::where('name', 'staff')->first();

        $this->owner = User::factory()->create()->assignRole('owner');
        $this->manager = User::factory()->create()->assignRole('manager');
        $this->staff = User::factory()->create()->assignRole('staff');
    }

    public function test_owner_has_all_permissions(): void
    {
        $allPermissions = Permission::all()->pluck('name')->toArray();

        foreach ($allPermissions as $permission) {
            $this->assertTrue($this->owner->can($permission), "Owner should have permission: {$permission}");
        }
    }

    public function test_manager_permissions(): void
    {
        $managerPermissions = [
            'view_dashboard',
            'view_orders',
            'view_order_details',
            'approve_orders',
            'change_order_status',
            'manage_order_courier',
            'mark_cod_collected',
            'print_invoices',
            'print_packing_slips',
            'export_orders',
            'cancel_orders',
            'view_products',
            'create_products',
            'edit_products',
            'manage_product_stock',
            'view_categories',
            'manage_categories',
            'view_colors',
            'manage_colors',
            'view_size_charts',
            'manage_size_charts',
            'view_banners',
            'manage_banners',
            'view_pages',
            'manage_pages',
            'view_customers',
            'manage_customers',
            'blacklist_customers',
            'view_coupons',
            'manage_coupons',
            'view_delivery_zones',
            'manage_delivery_zones',
            'view_districts',
            'manage_districts',
            'view_reports',
            'verify_bkash_payments',
            'payments.verify',
        ];

        foreach ($managerPermissions as $permission) {
            $this->assertTrue($this->manager->can($permission), "Manager should have permission: {$permission}");
        }

        // Manager should NOT have these permissions
        $deniedPermissions = [
            'view_cost_price',
            'manage_settings',
            'manage_payment_settings',
            'manage_delivery_settings',
            'view_users',
            'manage_users',
            'view_roles',
            'manage_roles',
            'view_profit_reports',
        ];

        foreach ($deniedPermissions as $permission) {
            $this->assertFalse($this->manager->can($permission), "Manager should NOT have permission: {$permission}");
        }
    }

    public function test_staff_permissions(): void
    {
        $staffPermissions = [
            'view_dashboard',
            'view_orders',
            'view_order_details',
            'approve_orders',
            'change_order_status',
            'manage_order_courier',
            'mark_cod_collected',
            'print_invoices',
            'print_packing_slips',
            'view_products',
            'manage_product_stock',
            'view_customers',
            'blacklist_customers',
        ];

        foreach ($staffPermissions as $permission) {
            $this->assertTrue($this->staff->can($permission), "Staff should have permission: {$permission}");
        }

        // Staff should NOT have these permissions
        $deniedPermissions = [
            'verify_bkash_payments',
            'create_products',
            'edit_products',
            'delete_products',
            'view_cost_price',
            'view_categories',
            'manage_categories',
            'view_colors',
            'manage_colors',
            'view_settings',
            'manage_settings',
            'view_users',
            'manage_users',
        ];

        foreach ($deniedPermissions as $permission) {
            $this->assertFalse($this->staff->can($permission), "Staff should NOT have permission: {$permission}");
        }
    }

    public function test_policy_order_view_any(): void
    {
        $this->assertTrue($this->owner->can('view_orders'));
        $this->assertTrue($this->manager->can('view_orders'));
        $this->assertTrue($this->staff->can('view_orders'));
    }

    public function test_policy_order_verify_bkash(): void
    {
        $this->assertTrue($this->owner->can('verify_bkash_payments'));
        $this->assertTrue($this->manager->can('verify_bkash_payments'));
        $this->assertFalse($this->staff->can('verify_bkash_payments'));
    }

    public function test_policy_product_view_cost_price(): void
    {
        $this->assertTrue($this->owner->can('view_cost_price'));
        $this->assertFalse($this->manager->can('view_cost_price'));
        $this->assertFalse($this->staff->can('view_cost_price'));
    }
}
