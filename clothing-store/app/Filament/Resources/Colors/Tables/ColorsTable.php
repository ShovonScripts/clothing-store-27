<?php

declare(strict_types=1);

namespace App\Filament\Resources\Colors\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('hex')
                    ->label(__('Color'))
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('Name (EN)'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('name_bn')
                    ->label(__('Name (BN)'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('Sort'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->width(80),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
