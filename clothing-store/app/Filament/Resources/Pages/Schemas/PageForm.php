<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('Content (EN)'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('Page Content'))
                                    ->schema([
                                        TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Page::class, 'slug', ignoreRecord: true)
                                            ->helperText(__('URL-friendly identifier')),
                                        TextInput::make('title')
                                            ->label(__('Title'))
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('body')
                                            ->label(__('Body'))
                                            ->required()
                                            ->columnSpanFull()
                                            ->rows(15),
                                        TextInput::make('meta_title')
                                            ->label(__('Meta Title'))
                                            ->maxLength(255)
                                            ->helperText(__('Max 60 characters')),
                                        TextInput::make('meta_description')
                                            ->label(__('Meta Description'))
                                            ->maxLength(500)
                                            ->helperText(__('Max 160 characters')),
                                    ])->columns(1),
                            ]),

                        Tab::make(__('Content (BN)'))
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make(__('Page Content (Bengali)'))
                                    ->schema([
                                        TextInput::make('title_bn')
                                            ->label(__('Title (Bengali)'))
                                            ->maxLength(255),
                                        Textarea::make('body_bn')
                                            ->label(__('Body (Bengali)'))
                                            ->columnSpanFull()
                                            ->rows(15),
                                    ])->columns(1),
                            ]),

                        Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make(__('Display'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('Active'))
                                            ->default(true),
                                    ])->columns(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
