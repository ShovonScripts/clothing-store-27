<?php

declare(strict_types=1);

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_desktop')
                    ->label(__('Desktop'))
                    ->square()
                    ->size(60),

                ImageColumn::make('image_mobile')
                    ->label(__('Mobile'))
                    ->square()
                    ->size(60),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(40),

                TextColumn::make('position')
                    ->label(__('Position'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hero' => 'primary',
                        'strip' => 'success',
                        'popup' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('sort_order')
                    ->label(__('Sort'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->width(80),

                TextColumn::make('starts_at')
                    ->label(__('Starts'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ends_at')
                    ->label(__('Ends'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('position')
                    ->label(__('Position'))
                    ->options([
                        'hero' => __('Hero'),
                        'strip' => __('Strip'),
                        'popup' => __('Popup'),
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
