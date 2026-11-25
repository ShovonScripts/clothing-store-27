<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeliveryZone;
use App\Models\District;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings defaults
        $this->seedSettings();

        // 2. Districts & Areas (Bangladesh)
        $this->seedDistrictsAndAreas();

        // 3. Delivery Zones
        $this->seedDeliveryZones();

        // 4. Colors
        $this->seedColors();

        // 5. Categories (empty tree - owner adds real categories)
        $this->seedCategories();

        // 6. Static Pages (empty - owner adds real content)
        $this->seedPages();

        // 7. Roles & Permissions
        $this->seedRolesAndPermissions();

        // 8. Owner User (from env, refuse empty/default password)
        $this->seedOwnerUser();
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => '{BRAND}', 'type' => 'string', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'whatsapp_number', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'facebook_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'tiktok_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'messenger_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'store_address', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'bkash_number', 'value' => '', 'type' => 'string', 'group' => 'payments'],
            ['key' => 'bkash_instructions_bn', 'value' => '', 'type' => 'string', 'group' => 'payments'],
            ['key' => 'bkash_instructions_en', 'value' => '', 'type' => 'string', 'group' => 'payments'],
            ['key' => 'bkash_verify_hours', 'value' => '24', 'type' => 'int', 'group' => 'payments'],
            ['key' => 'cod_enabled', 'value' => '1', 'type' => 'bool', 'group' => 'payments'],
            ['key' => 'bkash_enabled', 'value' => '1', 'type' => 'bool', 'group' => 'payments'],
            ['key' => 'delivery_inside_dhaka', 'value' => '70', 'type' => 'int', 'group' => 'delivery'],
            ['key' => 'delivery_outside_dhaka', 'value' => '130', 'type' => 'int', 'group' => 'delivery'],
            ['key' => 'free_delivery_over', 'value' => '500000', 'type' => 'int', 'group' => 'delivery'],
            ['key' => 'low_stock_threshold', 'value' => '5', 'type' => 'int', 'group' => 'inventory'],
            ['key' => 'max_pending_orders_per_phone_per_day', 'value' => '3', 'type' => 'int', 'group' => 'security'],
            ['key' => 'min_order_amount', 'value' => '0', 'type' => 'int', 'group' => 'orders'],
            ['key' => 'announcement_text', 'value' => '', 'type' => 'string', 'group' => 'marketing'],
            ['key' => 'announcement_enabled', 'value' => '0', 'type' => 'bool', 'group' => 'marketing'],
            ['key' => 'meta_pixel_id', 'value' => '', 'type' => 'string', 'group' => 'marketing'],
            ['key' => 'pixel_purchase_on', 'value' => 'approved', 'type' => 'string', 'group' => 'marketing'],
            ['key' => 'invoice_footer_note', 'value' => 'Thank you for shopping with us!', 'type' => 'string', 'group' => 'store'],
            ['key' => 'maintenance_orders_paused', 'value' => '0', 'type' => 'bool', 'group' => 'maintenance'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    private function seedDistrictsAndAreas(): void
    {
        // Only if tables are empty
        if (District::count() > 0) {
            return;
        }

        // Major districts for Bangladesh (simplified - owner can add full list)
        $districtsData = [
            ['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'zone' => 'inside_dhaka'],
            ['name' => 'Gazipur', 'name_bn' => 'গাজীপুর', 'zone' => 'inside_dhaka'],
            ['name' => 'Narayanganj', 'name_bn' => 'নারায়ণগঞ্জ', 'zone' => 'inside_dhaka'],
            ['name' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম', 'zone' => 'outside_dhaka'],
            ['name' => 'Rajshahi', 'name_bn' => 'রাজশাহী', 'zone' => 'outside_dhaka'],
            ['name' => 'Khulna', 'name_bn' => 'খুলনা', 'zone' => 'outside_dhaka'],
            ['name' => 'Barisal', 'name_bn' => 'বরিশাল', 'zone' => 'outside_dhaka'],
            ['name' => 'Sylhet', 'name_bn' => 'সিলেট', 'zone' => 'outside_dhaka'],
            ['name' => 'Rangpur', 'name_bn' => 'রংপুর', 'zone' => 'outside_dhaka'],
            ['name' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'zone' => 'outside_dhaka'],
        ];

        foreach ($districtsData as $d) {
            $district = District::create([
                'name' => $d['name'],
                'name_bn' => $d['name_bn'],
                'zone' => $d['zone'],
                'is_active' => true,
                'sort_order' => 1,
            ]);

            // Add a default area for each district
            Area::create([
                'district_id' => $district->id,
                'name' => $d['name'] . ' Sadar',
                'name_bn' => $d['name_bn'] . ' সদর',
                'is_active' => true,
            ]);
        }
    }

    private function seedDeliveryZones(): void
    {
        if (DeliveryZone::count() > 0) {
            return;
        }

        DeliveryZone::create([
            'key' => 'inside_dhaka',
            'name' => 'Inside Dhaka',
            'charge' => 70,
            'free_over' => 500000,
            'est_days_min' => 1,
            'est_days_max' => 2,
            'is_active' => true,
        ]);

        DeliveryZone::create([
            'key' => 'outside_dhaka',
            'name' => 'Outside Dhaka',
            'charge' => 130,
            'free_over' => 500000,
            'est_days_min' => 2,
            'est_days_max' => 5,
            'is_active' => true,
        ]);
    }

    private function seedColors(): void
    {
        if (Color::count() > 0) {
            return;
        }

        $colors = [
            ['name' => 'Black', 'name_bn' => 'কালো', 'hex' => '#000000', 'sort_order' => 1],
            ['name' => 'White', 'name_bn' => 'সাদা', 'hex' => '#FFFFFF', 'sort_order' => 2],
            ['name' => 'Navy', 'name_bn' => 'নেভি', 'hex' => '#001f3f', 'sort_order' => 3],
            ['name' => 'Grey', 'name_bn' => 'ধূসর', 'hex' => '#808080', 'sort_order' => 4],
            ['name' => 'Beige', 'name_bn' => 'বেজ', 'hex' => '#F5F5DC', 'sort_order' => 5],
        ];

        foreach ($colors as $c) {
            Color::create($c);
        }
    }

    private function seedCategories(): void
    {
        if (Category::count() > 0) {
            return;
        }

        // Create a root "Men's Fashion" category only - owner adds subcategories
        Category::create([
            'name' => 'Men\'s Fashion',
            'name_bn' => 'পুরুষ ফ্যাশন',
            'slug' => 'mens-fashion',
            'is_active' => true,
            'show_on_home' => false,
            'sort_order' => 1,
        ]);
    }

    private function seedPages(): void
    {
        if (Page::count() > 0) {
            return;
        }

        $pages = [
            ['slug' => 'about', 'title' => 'About Us', 'title_bn' => 'আমাদের সম্পর্কে', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
            ['slug' => 'contact', 'title' => 'Contact Us', 'title_bn' => 'যোগাযোগ করুন', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
            ['slug' => 'delivery-info', 'title' => 'Delivery Information', 'title_bn' => 'ডেলিভারি তথ্য', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
            ['slug' => 'return-exchange', 'title' => 'Return & Exchange', 'title_bn' => 'রিটার্ন ও এক্সচেঞ্জ', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'title_bn' => 'গোপনীয়তা নীতি', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
            ['slug' => 'terms', 'title' => 'Terms of Service', 'title_bn' => 'সেবার শর্তাবলী', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
            ['slug' => 'size-guide', 'title' => 'Size Guide', 'title_bn' => 'সাইজ গাইড', 'body' => '', 'body_bn' => '', 'is_active' => true, 'meta_title' => '', 'meta_description' => ''],
        ];

        foreach ($pages as $p) {
            Page::create($p);
        }
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            'view_orders',
            'manage_orders',
            'verify_bkash_payments',
            'view_customers',
            'manage_customers',
            'view_products',
            'manage_products',
            'view_categories',
            'manage_categories',
            'view_pages',
            'manage_pages',
            'view_banners',
            'manage_banners',
            'view_settings',
            'manage_settings',
            'view_reports',
            'manage_users',
            'view_cost_price',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view_orders', 'manage_orders', 'verify_bkash_payments',
            'view_customers', 'manage_customers',
            'view_products', 'manage_products',
            'view_categories', 'manage_categories',
            'view_pages', 'manage_pages',
            'view_banners', 'manage_banners',
            'view_reports',
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'view_orders',
            'view_customers',
            'view_products',
            'view_categories',
        ]);
    }

    private function seedOwnerUser(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Admin');

        if (empty($email) || empty($password)) {
            $this->command?->warn('ADMIN_EMAIL and ADMIN_PASSWORD must be set in .env to create owner user. Skipping.');
            return;
        }

        // Refuse default/empty password
        if ($password === 'password' || strlen($password) < 8) {
            $this->command?->error('ADMIN_PASSWORD must not be "password" or less than 8 characters. Owner user NOT created.');
            return;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        if (! $user->hasRole('owner')) {
            $user->assignRole('owner');
        }
    }
}