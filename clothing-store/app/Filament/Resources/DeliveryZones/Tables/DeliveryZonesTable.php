<?php

declare(strict_types=1);

namespace App\Filament\Resources\DeliveryZones\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeliveryZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->width(180),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('charge')
                    ->label(__('Charge'))
                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                    ->sortable()
                    ->alignEnd()
                    ->width(100),

                TextColumn::make('free_over')
                    ->label(__('Free Over'))
                    ->formatStateUsing(fn (?int $state) => $state ? '৳'.number_format($state) : __('Off'))
                    ->sortable()
                    ->alignEnd()
                    ->width(100),

                TextColumn::make('est_days_min')
                    ->label(__('Min Days'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->width(80),

                TextColumn::make('est_days_max')
                    ->label(__('Max Days'))
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
            ->defaultSort('charge')
            ->filters([
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
