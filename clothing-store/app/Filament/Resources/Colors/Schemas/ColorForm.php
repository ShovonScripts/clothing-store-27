<?php

declare(strict_types=1);

namespace App\Filament\Resources\Colors\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ColorForm
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
                            ->maxLength(255),
                        ColorPicker::make('hex')
                            ->label(__('Hex Color'))
                            ->helperText(__('Hex color code, e.g., #FF0000')),
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }
}
