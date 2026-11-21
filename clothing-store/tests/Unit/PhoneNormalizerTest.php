<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\PhoneNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNormalizerTest extends TestCase
{
    public function test_normalize_standard_bd_mobile(): void
    {
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('01712345678'));
    }

    public function test_normalize_with_plus_88_prefix(): void
    {
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('+8801712345678'));
    }

    public function test_normalize_with_880_prefix(): void
    {
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('8801712345678'));
    }

    public function test_normalize_with_bengali_digits(): void
    {
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('০১৭১২৩৪৫৬৭৮'));
    }

    public function test_normalize_with_spaces_and_dashes(): void
    {
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('017 123 456 78'));
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('017-123-456-78'));
        $this->assertEquals('01712345678', PhoneNormalizer::normalize('+880 17 123 456 78'));
    }

    public function test_normalize_invalid_prefix_010(): void
    {
        $this->assertEquals('01012345678', PhoneNormalizer::normalize('01012345678'));
    }

    public function test_is_valid_standard(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('01712345678'));
    }

    public function test_is_valid_with_plus_88(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('+8801712345678'));
    }

    public function test_is_valid_with_880(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('8801712345678'));
    }

    public function test_is_valid_with_bengali_digits(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('০১৭১২৩৪৫৬৭৮'));
    }

    public function test_is_valid_with_spaces(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('017 123 456 78'));
    }

    public function test_is_invalid_prefix_010(): void
    {
        $this->assertFalse(PhoneNormalizer::isValid('01012345678'));
    }

    public function test_is_invalid_too_short(): void
    {
        $this->assertFalse(PhoneNormalizer::isValid('0171234567'));
    }

    public function test_is_invalid_too_long(): void
    {
        $this->assertFalse(PhoneNormalizer::isValid('017123456789'));
    }

    public function test_is_invalid_landline(): void
    {
        $this->assertFalse(PhoneNormalizer::isValid('0212345678'));
    }

    public function test_format_display(): void
    {
        $this->assertEquals('017 123 456 78', PhoneNormalizer::format('01712345678'));
        $this->assertEquals('017 123 456 78', PhoneNormalizer::format('+8801712345678'));
    }

    public function test_mask_phone(): void
    {
        $this->assertEquals('017 *** 5678', PhoneNormalizer::mask('01712345678'));
        $this->assertEquals('017 *** 5678', PhoneNormalizer::mask('+8801712345678'));
    }
}
