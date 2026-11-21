<?php

declare(strict_types=1);

namespace App\Support;

class Money
{
    public static function format(int $taka, string $locale = 'bn'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, '৳');

        return $formatter->formatCurrency($taka, 'BDT');
    }

    public static function formatShort(int $taka): string
    {
        return '৳'.number_format($taka);
    }

    public static function parse(string $amount): int
    {
        // Remove currency symbol, commas, spaces
        $cleaned = preg_replace('/[^\d]/', '', $amount);

        return (int) $cleaned;
    }

    public static function add(int ...$amounts): int
    {
        return array_sum($amounts);
    }

    public static function subtract(int $total, int ...$amounts): int
    {
        return $total - array_sum($amounts);
    }
}
