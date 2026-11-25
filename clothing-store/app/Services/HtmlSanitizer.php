<?php

declare(strict_types=1);

namespace App\Services;

use Mews\Purifier\Facades\Purifier;

class HtmlSanitizer
{
    public function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        return Purifier::clean($html);
    }

    public function cleanSettings(array $settings): array
    {
        foreach ($settings as $key => $value) {
            if (is_string($value)) {
                $settings[$key] = $this->clean($value);
            }
        }

        return $settings;
    }

    public function cleanProductDescription(?string $description): ?string
    {
        return $this->clean($description);
    }

    public function cleanPageContent(?string $content): ?string
    {
        return $this->clean($content);
    }
}
