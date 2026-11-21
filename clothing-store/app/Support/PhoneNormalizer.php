<?php

declare(strict_types=1);

namespace App\Support;

class PhoneNormalizer
{
    public const BD_MOBILE_PATTERN = '/^(?:\+?88|0)?(1[3-9]\d{8})$/';

    public static function normalize(string $phone): string
    {
        // Handle Bengali digits first (before removing non-digits)
        $digits = self::bengaliToEnglish($phone);

        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $digits);

        // Normalize to 01XXXXXXXXXX format
        if (str_starts_with($digits, '880')) {
            $digits = '0'.substr($digits, 3);
        } elseif (str_starts_with($digits, '88')) {
            $digits = '0'.substr($digits, 2);
        } elseif (str_starts_with($digits, '1') && strlen($digits) === 10) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    public static function isValid(string $phone): bool
    {
        $normalized = self::normalize($phone);

        return preg_match(self::BD_MOBILE_PATTERN, $normalized) === 1;
    }

    public static function format(string $phone): string
    {
        $normalized = self::normalize($phone);

        if (strlen($normalized) === 11) {
            return substr($normalized, 0, 3).' '.substr($normalized, 3, 3).' '.substr($normalized, 6, 3).' '.substr($normalized, 9);
        }

        return $normalized;
    }

    public static function bengaliToEnglish(string $input): string
    {
        $bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($bengaliDigits, $englishDigits, $input);
    }

    public static function mask(string $phone): string
    {
        $normalized = self::normalize($phone);

        if (strlen($normalized) === 11) {
            return substr($normalized, 0, 3).' *** '.substr($normalized, -4);
        }

        return $normalized;
    }
}
