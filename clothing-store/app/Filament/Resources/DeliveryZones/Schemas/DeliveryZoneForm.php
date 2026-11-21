<?php

declare(strict_types=1);

namespace App\Filament\Resources\DeliveryZones\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeliveryZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->schema([
                        TextInput::make('key')
                            ->label(__('Key'))
                            ->required()
                            ->maxLength(50)
                            ->unique(DeliveryZone::class, 'key', ignoreRecord: true)
                            ->helperText(__('Unique identifier, e.g., inside_dhaka')),
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make(__('Delivery Settings'))
                    ->schema([
                        TextInput::make('charge')
                            ->label(__('Charge (৳)'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('৳'),
                        TextInput::make('free_over')
                            ->label(__('Free Delivery Over (৳)'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('৳')
                            ->helperText(__('0 to disable')),
                        TextInput::make('est_days_min')
                            ->label(__('Est. Min Days'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1),
                        TextInput::make('est_days_max')
                            ->label(__('Est. Max Days'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(5),
                    ])->columns(4),

                Section::make(__('Status'))
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->required()
                            ->default(true),
                    ])->columns(1),
            ]);
    }
}
