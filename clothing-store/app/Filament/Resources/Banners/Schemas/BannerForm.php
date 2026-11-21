<?php

declare(strict_types=1);

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->maxLength(255),
                        FileUpload::make('image_desktop')
                            ->label(__('Desktop Image'))
                            ->image()
                            ->directory('banners')
                            ->imagePreviewHeight('150')
                            ->maxSize(5120),
                        FileUpload::make('image_mobile')
                            ->label(__('Mobile Image'))
                            ->image()
                            ->directory('banners')
                            ->imagePreviewHeight('150')
                            ->maxSize(5120),
                        TextInput::make('link_url')
                            ->label(__('Link URL'))
                            ->url()
                            ->placeholder('https://example.com'),
                    ])->columns(2),

                Section::make(__('Display Settings'))
                    ->schema([
                        Select::make('position')
                            ->label(__('Position'))
                            ->options([
                                'hero' => __('Hero'),
                                'strip' => __('Strip'),
                                'popup' => __('Popup'),
                            ])
                            ->required()
                            ->default('hero')
                            ->native(false),
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        DateTimePicker::make('starts_at')
                            ->label(__('Starts At'))
                            ->native(false),
                        DateTimePicker::make('ends_at')
                            ->label(__('Ends At'))
                            ->native(false),
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->required()
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
