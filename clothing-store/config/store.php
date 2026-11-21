<?php

declare(strict_types=1);

return [
    'name' => env('STORE_NAME', '{BRAND}'),
    'description' => env('STORE_DESCRIPTION', 'Men\'s fashion store for Bangladesh'),
    'currency' => 'BDT',
    'currency_symbol' => '৳',
    'locale' => 'bn',
    'fallback_locale' => 'en',
    'timezone' => 'Asia/Dhaka',
    'phone_format' => '01XXXXXXXXX',
    'order_number_format' => 'YYMMDD####',
    'low_stock_threshold' => 5,
    'max_pending_orders_per_phone_per_day' => 3,
    'bkash_verify_hours' => 24,
];
