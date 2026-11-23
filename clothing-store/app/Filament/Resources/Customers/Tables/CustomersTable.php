<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                TextColumn::make('total_orders')
                    ->label(__('Total Orders'))
                    ->sortable()
                    ->alignCenter()
                    ->numeric(),

                TextColumn::make('delivered_orders')
                    ->label(__('Delivered'))
                    ->sortable()
                    ->alignCenter()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('cancelled_orders')
                    ->label(__('Cancelled'))
                    ->sortable()
                    ->alignCenter()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_spent')
                    ->label(__('Total Spent'))
                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pending_orders_today')
                    ->label(__('Pending Today'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_order_date')
                    ->label(__('Last Order'))
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                IconColumn::make('is_blacklisted')
                    ->label(__('Blacklisted'))
                    ->boolean()
                    ->trueIcon('heroicon-o-ban')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->defaultSort('desc')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('is_blacklisted')
                    ->label(__('Blacklisted'))
                    ->options([
                        '1' => __('Yes'),
                        '0' => __('No'),
                    ]),

                Filter::make('created_at')
                    ->label(__('Date Range'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('From')),
                        DatePicker::make('created_until')
                            ->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['created_until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('blacklist')
                        ->label(__('Blacklist Selected'))
                        ->icon('heroicon-o-ban')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('reason')
                                ->label(__('Blacklist Reason'))
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if (! $record->is_blacklisted) {
                                    $record->update([
                                        'is_blacklisted' => true,
                                        'blacklist_reason' => $data['reason'],
                                    ]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('unblacklist')
                        ->label(__('Remove Blacklist'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->is_blacklisted) {
                                    $record->update([
                                        'is_blacklisted' => false,
                                        'blacklist_reason' => null,
                                    ]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
