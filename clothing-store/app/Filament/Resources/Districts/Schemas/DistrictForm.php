<?php

declare(strict_types=1);

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name (English)'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name_bn')
                            ->label(__('Name (Bengali)'))
                            ->required()
                            ->maxLength(255),
                        Select::make('zone')
                            ->label(__('Delivery Zone'))
                            ->options([
                                'inside_dhaka' => __('Inside Dhaka'),
                                'outside_dhaka' => __('Outside Dhaka'),
                            ])
                            ->required()
                            ->default('outside_dhaka')
                            ->native(false),
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

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
