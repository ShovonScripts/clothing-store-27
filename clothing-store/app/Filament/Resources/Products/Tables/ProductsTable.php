<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('firstMediaUrl')
                    ->label(__('Image'))
                    ->getStateUsing(fn (Product $record) => $record->getFirstMediaUrl('gallery', 'thumb'))
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('Name (EN)'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(40),

                TextColumn::make('name_bn')
                    ->label(__('Name (BN)'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(40),

                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('price')
                    ->label(__('Price'))
                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('total_stock')
                    ->label(__('Total Stock'))
                    ->formatStateUsing(fn (Product $record) => $record->variants()->sum('stock'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (int $state) => $state <= 5 ? 'danger' : ($state <= 20 ? 'warning' : 'success')),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('is_new')
                    ->label(__('New'))
                    ->boolean()
                    ->trueIcon('heroicon-o-sparkles')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getDisplayNameAttribute()),

                SelectFilter::make('is_active')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive'),
                    ]),

                SelectFilter::make('is_featured')
                    ->label(__('Featured'))
                    ->options([
                        '1' => __('Yes'),
                        '0' => __('No'),
                    ]),

                SelectFilter::make('is_new')
                    ->label(__('New Arrival'))
                    ->options([
                        '1' => __('Yes'),
                        '0' => __('No'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // Bulk actions
            ]);
    }
}
