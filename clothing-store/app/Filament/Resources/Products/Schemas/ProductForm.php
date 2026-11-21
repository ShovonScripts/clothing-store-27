<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('General'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('Basic Information'))
                                    ->schema([
                                        Select::make('category_id')
                                            ->label(__('Category'))
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getDisplayNameAttribute()),
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
                                            ->unique(Product::class, 'slug', ignoreRecord: true),
                                        TextInput::make('sku')
                                            ->label(__('SKU'))
                                            ->maxLength(100)
                                            ->unique(Product::class, 'sku', ignoreRecord: true),
                                        TextInput::make('price')
                                            ->label(__('Price (৳)'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('৳'),
                                        TextInput::make('compare_at_price')
                                            ->label(__('Compare At Price (৳)'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('৳'),
                                        TextInput::make('cost_price')
                                            ->label(__('Cost Price (৳)'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('৳')
                                            ->visible(fn () => auth()->user()?->hasRole('owner')),
                                    ])->columns(2),

                                Section::make(__('Descriptions'))
                                    ->schema([
                                        TextInput::make('fabric')
                                            ->label(__('Fabric'))
                                            ->maxLength(255),
                                        TextInput::make('fit')
                                            ->label(__('Fit'))
                                            ->maxLength(50),
                                        Textarea::make('short_description')
                                            ->label(__('Short Description'))
                                            ->columnSpanFull()
                                            ->rows(3),
                                        Textarea::make('description')
                                            ->label(__('Full Description'))
                                            ->columnSpanFull()
                                            ->rows(6),
                                        Textarea::make('care')
                                            ->label(__('Care Instructions'))
                                            ->columnSpanFull()
                                            ->rows(3),
                                    ])->columns(2),

                                Section::make(__('Display'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('Active'))
                                            ->default(true),
                                        Toggle::make('is_featured')
                                            ->label(__('Featured'))
                                            ->default(false),
                                        Toggle::make('is_new')
                                            ->label(__('New Arrival'))
                                            ->default(false),
                                        Select::make('size_chart_id')
                                            ->label(__('Size Chart'))
                                            ->relationship('sizeChart', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(4),
                            ]),

                        Tab::make(__('Images'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('gallery')
                                    ->label(__('Product Images'))
                                    ->multiple()
                                    ->reorderable()
                                    ->image()
                                    ->directory('products')
                                    ->imagePreviewHeight('150')
                                    ->maxSize(5120)
                                    ->maxFiles(10)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('Variants'))
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Section::make(__('Product Variants'))
                                    ->description(__('Manage size × color variants with stock and price overrides'))
                                    ->schema([
                                        Repeater::make('variants')
                                            ->label('')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        Select::make('size')
                                                            ->label(__('Size'))
                                                            ->options([
                                                                'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL', '3XL' => '3XL',
                                                                '28' => '28', '30' => '30', '32' => '32', '34' => '34', '36' => '36', '38' => '38', '40' => '40',
                                                                'Free' => 'Free',
                                                            ])
                                                            ->required()
                                                            ->native(false),
                                                        Select::make('color_id')
                                                            ->label(__('Color'))
                                                            ->options(Color::all()->pluck('name', 'id')->toArray())
                                                            ->searchable()
                                                            ->preload()
                                                            ->required(),
                                                        TextInput::make('price')
                                                            ->label(__('Price Override (৳)'))
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->prefix('৳')
                                                            ->helperText(__('Leave empty to use product price')),
                                                        TextInput::make('stock')
                                                            ->label(__('Stock'))
                                                            ->required()
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->default(0),
                                                        TextInput::make('sku')
                                                            ->label(__('SKU'))
                                                            ->maxLength(100)
                                                            ->unique(ProductVariant::class, 'sku', ignoreRecord: true),
                                                        Toggle::make('is_active')
                                                            ->label(__('Active'))
                                                            ->default(true),
                                                    ])->columns(2),
                                            ])
                                            ->addActionLabel(__('Add Variant'))
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => ($state['size'] ?? '').' / '.($state['color_id'] ? Color::find($state['color_id'])?->getDisplayNameAttribute() : '')
                                            )
                                            ->columns(1),
                                    ])
                                    ->columns(1),
                            ]),

                        Tab::make(__('SEO'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make(__('Search Engine Optimization'))
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label(__('Meta Title'))
                                            ->maxLength(255)
                                            ->helperText(__('Max 60 characters recommended')),
                                        Textarea::make('meta_description')
                                            ->label(__('Meta Description'))
                                            ->maxLength(500)
                                            ->rows(3)
                                            ->helperText(__('Max 160 characters recommended')),
                                    ])->columns(1),
                            ]),

                        Tab::make(__('Display'))
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Section::make(__('Display Options'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('Active'))
                                            ->default(true),
                                        Toggle::make('is_featured')
                                            ->label(__('Featured'))
                                            ->default(false),
                                        Toggle::make('is_new')
                                            ->label(__('New Arrival'))
                                            ->default(false),
                                        DateTimePicker::make('published_at')
                                            ->label(__('Published At'))
                                            ->native(false),
                                    ])->columns(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
