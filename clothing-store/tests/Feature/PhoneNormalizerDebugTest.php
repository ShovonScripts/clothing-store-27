<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Support\PhoneNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhoneNormalizerDebugTest extends TestCase
{
    #[Test]
    public function debug_normalize(): void
    {
        $phone1 = '8801712345678';
        $phone2 = '01712345678';

        $norm1 = PhoneNormalizer::normalize($phone1);
        $norm2 = PhoneNormalizer::normalize($phone2);

        echo "Phone 1: $phone1 -> Normalized: $norm1\n";
        echo "Phone 2: $phone2 -> Normalized: $norm2\n";
        echo 'Equal: '.($norm1 === $norm2 ? 'YES' : 'NO')."\n";

        // Test with various formats
        $testPhones = [
            '8801712345678',
            '01712345678',
            '+8801712345678',
            '+88 01712345678',
            '88 01712345678',
            '01712345678',
        ];

        foreach ($testPhones as $phone) {
            $norm = PhoneNormalizer::normalize($phone);
            echo "$phone -> $norm\n";
        }
    }
}
