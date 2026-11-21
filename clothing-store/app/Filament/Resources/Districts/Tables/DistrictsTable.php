<?php

declare(strict_types=1);

namespace App\Filament\Resources\Districts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DistrictsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name (EN)'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('name_bn')
                    ->label(__('Name (BN)'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('zone')
                    ->label(__('Zone'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inside_dhaka' => 'success',
                        'outside_dhaka' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('sort_order')
                    ->label(__('Sort'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->width(80),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->width(80),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('zone')
                    ->label(__('Zone'))
                    ->options([
                        'inside_dhaka' => __('Inside Dhaka'),
                        'outside_dhaka' => __('Outside Dhaka'),
                    ]),
                SelectFilter::make('is_active')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
