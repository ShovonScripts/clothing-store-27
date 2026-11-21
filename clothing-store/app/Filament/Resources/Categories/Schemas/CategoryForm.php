<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->schema([
                        Select::make('parent_id')
                            ->label(__('Parent Category'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->getDisplayNameAttribute()),
                        TextInput::make('name')
                            ->label(__('Name (English)'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name_bn')
                            ->label(__('Name (Bengali)'))
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(Category::class, 'slug', ignoreRecord: true),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        FileUpload::make('image_path')
                            ->label(__('Image'))
                            ->image()
                            ->directory('categories')
                            ->imagePreviewHeight('150')
                            ->maxSize(5120),
                    ])->columns(2),

                Section::make(__('Display Settings'))
                    ->schema([
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(__('Active'))
                            ->required()
                            ->default(true),
                        Toggle::make('show_on_home')
                            ->label(__('Show on Homepage'))
                            ->required()
                            ->default(false),
                    ])->columns(3),

                Section::make(__('SEO'))
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('Meta Title'))
                            ->maxLength(255),
                        TextInput::make('meta_description')
                            ->label(__('Meta Description'))
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }
}
