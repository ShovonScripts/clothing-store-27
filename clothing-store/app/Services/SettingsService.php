<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    public const CACHE_KEY = 'store_settings';

    public const CACHE_TTL = 3600; // 1 hour

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->getAll();

        return $settings[$key] ?? $default;
    }

    public function getAll(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return DB::table('settings')
                ->select('key', 'value', 'type')
                ->get()
                ->mapWithKeys(function ($setting) {
                    $castValue = $this->castValue($setting->value, $setting->type ?? 'string');

                    return [$setting->key => $castValue];
                })
                ->toArray();
        });
    }

    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => $this->serializeValue($value, $type),
                'type' => $type,
                'group' => $group,
            ]
        );

        $this->clearCache();
    }

    public function forget(string $key): void
    {
        DB::table('settings')->where('key', $key)->delete();
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int' => (int) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    private function serializeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'json' => json_encode($value),
            'bool' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    // Typed getters for common settings
    public function storeName(): string
    {
        return $this->get('store_name', '{BRAND}');
    }

    public function storePhone(): string
    {
        return $this->get('store_phone', '01XXXXXXXXX');
    }

    public function whatsappNumber(): string
    {
        return $this->get('whatsapp_number', '');
    }

    public function bkashNumber(): string
    {
        return $this->get('bkash_number', '01XXXXXXXXX');
    }

    public function bkashInstructions(string $locale = 'bn'): string
    {
        return $this->get("bkash_instructions_{$locale}", '');
    }

    public function bkashVerifyHours(): int
    {
        return $this->get('bkash_verify_hours', 24);
    }

    public function codEnabled(): bool
    {
        return $this->get('cod_enabled', true);
    }

    public function bkashEnabled(): bool
    {
        return $this->get('bkash_enabled', true);
    }

    public function deliveryInsideDhaka(): int
    {
        return $this->get('delivery_inside_dhaka', 70);
    }

    public function deliveryOutsideDhaka(): int
    {
        return $this->get('delivery_outside_dhaka', 130);
    }

    public function freeDeliveryOver(): int
    {
        return $this->get('free_delivery_over', 0);
    }

    public function lowStockThreshold(): int
    {
        return $this->get('low_stock_threshold', 5);
    }

    public function maxPendingOrdersPerPhonePerDay(): int
    {
        return $this->get('max_pending_orders_per_phone_per_day', 3);
    }

    public function minOrderAmount(): int
    {
        return $this->get('min_order_amount', 0);
    }

    public function announcementText(): string
    {
        return $this->get('announcement_text', '');
    }

    public function announcementEnabled(): bool
    {
        return $this->get('announcement_enabled', false);
    }

    public function metaPixelId(): string
    {
        return $this->get('meta_pixel_id', '');
    }

    public function invoiceFooterNote(): string
    {
        return $this->get('invoice_footer_note', '');
    }

    public function maintenanceOrdersPaused(): bool
    {
        return $this->get('maintenance_orders_paused', false);
    }
}
