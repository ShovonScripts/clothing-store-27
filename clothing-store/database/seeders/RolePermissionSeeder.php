<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view_dashboard' => 'View Dashboard',

            // Orders
            'view_orders' => 'View Orders',
            'view_order_details' => 'View Order Details',
            'approve_orders' => 'Approve/Reject Orders',
            'verify_bkash_payments' => 'Verify/Reject bKash Payments',
            'change_order_status' => 'Change Order Status',
            'manage_order_courier' => 'Manage Courier/Consignment',
            'mark_cod_collected' => 'Mark COD Collected',
            'print_invoices' => 'Print Invoices',
            'print_packing_slips' => 'Print Packing Slips',
            'export_orders' => 'Export Orders',
            'cancel_orders' => 'Cancel Orders',

            // Products
            'view_products' => 'View Products',
            'create_products' => 'Create Products',
            'edit_products' => 'Edit Products',
            'delete_products' => 'Delete Products',
            'manage_product_stock' => 'Manage Product Stock',
            'view_cost_price' => 'View Cost Price',

            // Categories
            'view_categories' => 'View Categories',
            'manage_categories' => 'Manage Categories',

            // Colors
            'view_colors' => 'View Colors',
            'manage_colors' => 'Manage Colors',

            // Size Charts
            'view_size_charts' => 'View Size Charts',
            'manage_size_charts' => 'Manage Size Charts',

            // Banners
            'view_banners' => 'View Banners',
            'manage_banners' => 'Manage Banners',

            // Pages
            'view_pages' => 'View Pages',
            'manage_pages' => 'Manage Pages',

            // Customers
            'view_customers' => 'View Customers',
            'manage_customers' => 'Manage Customers',
            'blacklist_customers' => 'Blacklist/Unblacklist Customers',

            // Coupons
            'view_coupons' => 'View Coupons',
            'manage_coupons' => 'Manage Coupons',

            // Settings
            'view_settings' => 'View Settings',
            'manage_settings' => 'Manage Settings',
            'manage_payment_settings' => 'Manage Payment Settings',
            'manage_delivery_settings' => 'Manage Delivery Settings',

            // Delivery
            'view_delivery_zones' => 'View Delivery Zones',
            'manage_delivery_zones' => 'Manage Delivery Zones',
            'view_districts' => 'View Districts',
            'manage_districts' => 'Manage Districts',

            // Users & Roles
            'view_users' => 'View Users',
            'manage_users' => 'Manage Users',
            'view_roles' => 'View Roles',
            'manage_roles' => 'Manage Roles',

            // Reports
            'view_reports' => 'View Reports',
            'view_profit_reports' => 'View Profit Reports',

            // Activity Log
            'view_activity_log' => 'View Activity Log',
        ];

        foreach ($permissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Create roles
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        // Owner gets all permissions
        $owner->givePermissionTo(Permission::all());

        // Manager permissions
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
        ];
        $manager->givePermissionTo($managerPermissions);

        // Staff permissions
        $staffPermissions = [
            'view_dashboard',
            'view_orders',
            'view_order_details',
            'approve_orders', // setting-controlled
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
        $staff->givePermissionTo($staffPermissions);

        $this->command?->info('Roles and permissions seeded.');
    }
}
