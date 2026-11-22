<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => '{BRAND}', 'type' => 'string', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '01XXXXXXXXX', 'type' => 'string', 'group' => 'store'],
            ['key' => 'whatsapp_number', 'value' => '8801XXXXXXXXX', 'type' => 'string', 'group' => 'store'],
            ['key' => 'facebook_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'tiktok_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'messenger_url', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'store_address', 'value' => '', 'type' => 'string', 'group' => 'store'],
            ['key' => 'bkash_number', 'value' => '01XXXXXXXXX', 'type' => 'string', 'group' => 'payments'],
            ['key' => 'bkash_instructions_bn', 'value' => 'বিকাশ পেমেন্টের জন্য ০১XXXXXXXXX নম্বরে টাকা পাঠান। তারপর TrxID এবং 보낸 নম্বর দিন।', 'type' => 'string', 'group' => 'payments'],
            ['key' => 'bkash_instructions_en', 'value' => 'Send money to 01XXXXXXXXX via bKash. Then enter TrxID and sender number.', 'type' => 'string', 'group' => 'payments'],
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
            ['key' => 'invoice_footer_note', 'value' => 'Thank you for shopping with us!', 'type' => 'string', 'group' => 'store'],
            ['key' => 'maintenance_orders_paused', 'value' => '0', 'type' => 'bool', 'group' => 'maintenance'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
