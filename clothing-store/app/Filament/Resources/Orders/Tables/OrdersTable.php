<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Tables;

use App\Actions\ApproveOrderAction;
use App\Actions\CancelOrderAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('Order #'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage(__('Order number copied'))
                    ->limit(20),

                TextColumn::make('customer.name')
                    ->label(__('Customer'))
                    ->searchable(['customers.name', 'customers.phone'])
                    ->sortable()
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('shipping_phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                TextColumn::make('shipping_name')
                    ->label(__('Shipping To'))
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'warning' => OrderStatus::Pending,
                        'info' => OrderStatus::Confirmed,
                        'primary' => OrderStatus::Processing,
                        'purple' => OrderStatus::Shipped,
                        'success' => OrderStatus::Delivered,
                        'danger' => OrderStatus::Cancelled,
                        'orange' => OrderStatus::Returned,
                        'orange' => OrderStatus::FailedDelivery,
                    ])
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label(__('Payment'))
                    ->badge()
                    ->colors([
                        'gray' => PaymentMethod::Cod,
                        'purple' => PaymentMethod::BkashSendMoney,
                    ])
                    ->formatStateUsing(fn (PaymentMethod $state) => $state->label())
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->colors([
                        'gray' => PaymentStatus::Unpaid,
                        'warning' => PaymentStatus::AwaitingVerification,
                        'success' => PaymentStatus::Paid,
                        'danger' => PaymentStatus::Rejected,
                        'gray' => PaymentStatus::Refunded,
                    ])
                    ->formatStateUsing(fn (PaymentStatus $state) => $state->label())
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('Total'))
                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                    ->sortable()
                    ->alignEnd()
                    ->weight('medium'),

                TextColumn::make('created_at')
                    ->label(__('Ordered'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->defaultSort('desc')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Order Status'))
                    ->options(OrderStatus::class)
                    ->multiple()
                    ->preload(),

                SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options(PaymentMethod::class)
                    ->multiple()
                    ->preload(),

                SelectFilter::make('payment_status')
                    ->label(__('Payment Status'))
                    ->options(PaymentStatus::class)
                    ->multiple()
                    ->preload(),

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
                Action::make('view')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record) => static::getResource()::getUrl('view', ['record' => $record]))
                    ->color('primary'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label(__('Approve Selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === OrderStatus::Pending && $record->payment_method !== PaymentMethod::BkashSendMoney) {
                                    app(ApproveOrderAction::class)->execute($record, Filament::getAuthUser());
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('cancel')
                        ->label(__('Cancel Selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('reason')
                                ->label(__('Cancellation Reason'))
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status->canTransitionTo(OrderStatus::Cancelled)) {
                                    app(CancelOrderAction::class)->execute($record, Filament::getAuthUser(), $data['reason']);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('export')
                        ->label(__('Export CSV'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            // Export logic will be implemented
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
