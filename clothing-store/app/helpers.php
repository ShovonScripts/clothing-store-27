<?php

declare(strict_types=1);

use App\Services\SettingsService;
use App\Support\Money;
use App\Support\PhoneNormalizer;

if (! function_exists('settings')) {
    /**
     * Get a setting value by key.
     */
    function settings(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format money amount in BDT.
     */
    function money(int $taka, string $locale = 'bn'): string
    {
        return Money::format($taka, $locale);
    }
}

if (! function_exists('moneyShort')) {
    /**
     * Format money amount in BDT (short format).
     */
    function moneyShort(int $taka): string
    {
        return Money::formatShort($taka);
    }
}

if (! function_exists('normalizePhone')) {
    /**
     * Normalize Bangladeshi phone number.
     */
    function normalizePhone(string $phone): string
    {
        return PhoneNormalizer::normalize($phone);
    }
}

if (! function_exists('isValidBdPhone')) {
    /**
     * Check if phone number is a valid Bangladeshi mobile number.
     */
    function isValidBdPhone(string $phone): bool
    {
        return PhoneNormalizer::isValid($phone);
    }
}

if (! function_exists('formatPhone')) {
    /**
     * Format phone number for display.
     */
    function formatPhone(string $phone): string
    {
        return PhoneNormalizer::format($phone);
    }
}

if (! function_exists('maskPhone')) {
    /**
     * Mask phone number for privacy.
     */
    function maskPhone(string $phone): string
    {
        return PhoneNormalizer::mask($phone);
    }
}
