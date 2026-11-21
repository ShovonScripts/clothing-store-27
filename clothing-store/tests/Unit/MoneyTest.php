<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_format_short(): void
    {
        $this->assertEquals('৳1,250', Money::formatShort(1250));
        $this->assertEquals('৳0', Money::formatShort(0));
        $this->assertEquals('৳10,000', Money::formatShort(10000));
    }

    public function test_format_with_locale_bn(): void
    {
        $result = Money::format(1250, 'bn');
        $this->assertStringContainsString('৳', $result);
        // Bengali locale uses Bengali digits (১২৩৪৫৬৭৮৯০)
        $this->assertTrue(
            str_contains($result, '১') || str_contains($result, '1'),
            "Expected digit in: {$result}"
        );
    }

    public function test_format_with_locale_en(): void
    {
        $result = Money::format(1250, 'en');
        // NumberFormatter may use BDT or ৳ depending on locale data
        $this->assertTrue(
            str_contains($result, '৳') || str_contains($result, 'BDT'),
            "Expected currency symbol in: {$result}"
        );
    }

    public function test_parse(): void
    {
        $this->assertEquals(1250, Money::parse('৳1,250'));
        $this->assertEquals(1250, Money::parse('1,250'));
        $this->assertEquals(1250, Money::parse('1250'));
        $this->assertEquals(0, Money::parse('৳0'));
    }

    public function test_add(): void
    {
        $this->assertEquals(3000, Money::add(1000, 2000));
        $this->assertEquals(5000, Money::add(1000, 2000, 2000));
    }

    public function test_subtract(): void
    {
        $this->assertEquals(500, Money::subtract(1000, 500));
        $this->assertEquals(0, Money::subtract(1000, 500, 500));
        $this->assertEquals(-500, Money::subtract(1000, 1500));
    }
}
