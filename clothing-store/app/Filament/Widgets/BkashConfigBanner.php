<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\SettingsService;
use Filament\Widgets\Widget;

class BkashConfigBanner extends Widget
{
    protected string $view = 'filament.widgets.bkash-config-banner';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $settings = app(SettingsService::class);

        return [
            'isConfigured' => $settings->isBkashConfigured(),
            'bkashNumber' => $settings->bkashNumber(),
            'bkashEnabled' => $settings->bkashEnabled(),
        ];
    }
}