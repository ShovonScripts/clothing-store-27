<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Pages;

use App\Actions\AddOrderNoteAction;
use App\Actions\ApproveOrderAction;
use App\Actions\CancelOrderAction;
use App\Actions\MarkCodCollectedAction;
use App\Actions\MarkDeliveredAction;
use App\Actions\MarkFailedDeliveryAction;
use App\Actions\ProcessOrderAction;
use App\Actions\RejectBkashPaymentAction;
use App\Actions\ReturnOrderAction;
use App\Actions\ShipOrderAction;
use App\Actions\VerifyBkashPaymentAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->record;

        return [
            Action::make('approve')
                ->label(__('Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Pending && $record->payment_method !== PaymentMethod::BkashSendMoney)
                ->requiresConfirmation()
                ->action(fn (Order $record) => app(ApproveOrderAction::class)->execute($record, Filament::getAuthUser()))
                ->successNotificationTitle(__('Order approved')),

            Action::make('verify_bkash')
                ->label(__('Verify bKash'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (Order $record) => $record->isBkash() && $record->status === OrderStatus::Pending && $record->payment_status === PaymentStatus::AwaitingVerification)
                ->requiresConfirmation()
                ->action(fn (Order $record) => app(VerifyBkashPaymentAction::class)->execute($record, Filament::getAuthUser()))
                ->successNotificationTitle(__('bKash payment verified')),

            Action::make('reject_bkash')
                ->label(__('Reject bKash'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (Order $record) => $record->isBkash() && $record->status === OrderStatus::Pending && $record->payment_status === PaymentStatus::AwaitingVerification)
                ->form([
                    Textarea::make('reason')
                        ->label(__('Rejection Reason'))
                        ->required()
                        ->rows(3),
                ])
                ->action(fn (Order $record, array $data) => app(RejectBkashPaymentAction::class)->execute($record, Filament::getAuthUser(), $data['reason']))
                ->successNotificationTitle(__('bKash payment rejected')),

            Action::make('cancel')
                ->label(__('Cancel Order'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (Order $record) => $record->status->canTransitionTo(OrderStatus::Cancelled))
                ->requiresConfirmation()
                ->form([
                    Textarea::make('reason')
                        ->label(__('Cancellation Reason'))
                        ->required()
                        ->rows(3),
                ])
                ->action(fn (Order $record, array $data) => app(CancelOrderAction::class)->execute($record, Filament::getAuthUser(), $data['reason']))
                ->successNotificationTitle(__('Order cancelled')),

            Action::make('process')
                ->label(__('Process'))
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Confirmed)
                ->action(fn (Order $record) => app(ProcessOrderAction::class)->execute($record, Filament::getAuthUser()))
                ->successNotificationTitle(__('Order moved to processing')),

            Action::make('ship')
                ->label(__('Ship'))
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Processing)
                ->form([
                    Grid::make(2)->schema([
                        TextInput::make('courier')
                            ->label(__('Courier'))
                            ->required(),
                        TextInput::make('consignment_id')
                            ->label(__('Consignment ID'))
                            ->required(),
                        TextInput::make('tracking_url')
                            ->label(__('Tracking URL'))
                            ->url()
                            ->placeholder('https://track.courier.com/...'),
                    ]),
                ])
                ->action(fn (Order $record, array $data) => app(ShipOrderAction::class)->execute($record, Filament::getAuthUser(), $data['courier'], $data['consignment_id'], $data['tracking_url'] ?? null))
                ->successNotificationTitle(__('Order shipped')),

            Action::make('deliver')
                ->label(__('Mark Delivered'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Shipped)
                ->action(fn (Order $record) => app(MarkDeliveredAction::class)->execute($record, Filament::getAuthUser()))
                ->successNotificationTitle(__('Order marked as delivered')),

            Action::make('failed_delivery')
                ->label(__('Failed Delivery'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Shipped)
                ->action(fn (Order $record) => app(MarkFailedDeliveryAction::class)->execute($record, Filament::getAuthUser()))
                ->successNotificationTitle(__('Delivery marked as failed')),

            Action::make('return')
                ->label(__('Return'))
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->visible(fn (Order $record) => $record->status === OrderStatus::Delivered)
                ->form([
                    Toggle::make('restock')
                        ->label(__('Restock items'))
                        ->default(false),
                    Textarea::make('note')
                        ->label(__('Return Note'))
                        ->rows(2),
                ])
                ->action(fn (Order $record, array $data) => app(ReturnOrderAction::class)->execute($record, Filament::getAuthUser(), $data['restock'] ?? false, $data['note'] ?? null))
                ->successNotificationTitle(__('Order returned')),

            Action::make('collect_cod')
                ->label(__('Collect COD'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->visible(fn (Order $record) => $record->isCod() && $record->status === OrderStatus::Delivered && $record->payment_status !== PaymentStatus::Paid)
                ->action(fn (Order $record) => app(MarkCodCollectedAction::class)->execute($record, Filament::getAuthUser()))
                ->successNotificationTitle(__('COD collected')),

            Action::make('add_note')
                ->label(__('Add Note'))
                ->icon('heroicon-o-pencil')
                ->color('gray')
                ->form([
                    Textarea::make('note')
                        ->label(__('Note'))
                        ->required()
                        ->rows(3),
                ])
                ->action(fn (Order $record, array $data) => app(AddOrderNoteAction::class)->execute($record, Filament::getAuthUser(), $data['note']))
                ->successNotificationTitle(__('Note added')),

            Action::make('print_invoice')
                ->label(__('Print Invoice'))
                ->icon('heroicon-o-printer')
                ->url(fn (Order $record) => route('orders.invoice', $record))
                ->openUrlInNewTab(),

            Action::make('print_packing_slip')
                ->label(__('Print Packing Slip'))
                ->icon('heroicon-o-document-text')
                ->url(fn (Order $record) => route('orders.packing-slip', $record))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Split::make([
                    InfolistSection::make(__('Order Information'))
                        ->schema([
                            InfolistGrid::make(2)->schema([
                                TextEntry::make('order_number')
                                    ->label(__('Order Number'))
                                    ->copyable()
                                    ->copyMessage(__('Copied')),
                                TextEntry::make('created_at')
                                    ->label(__('Order Date'))
                                    ->dateTime('M d, Y H:i'),
                                TextEntry::make('status')
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
                                    ->formatStateUsing(fn (OrderStatus $state) => $state->label()),
                                TextEntry::make('payment_method')
                                    ->label(__('Payment Method'))
                                    ->badge()
                                    ->colors([
                                        'gray' => PaymentMethod::Cod,
                                        'purple' => PaymentMethod::BkashSendMoney,
                                    ])
                                    ->formatStateUsing(fn (PaymentMethod $state) => $state->label()),
                                TextEntry::make('payment_status')
                                    ->label(__('Payment Status'))
                                    ->badge()
                                    ->colors([
                                        'gray' => PaymentStatus::Unpaid,
                                        'warning' => PaymentStatus::AwaitingVerification,
                                        'success' => PaymentStatus::Paid,
                                        'danger' => PaymentStatus::Rejected,
                                        'gray' => PaymentStatus::Refunded,
                                    ])
                                    ->formatStateUsing(fn (PaymentStatus $state) => $state->label()),
                            ]),
                        ]),

                    InfolistSection::make(__('Customer & Shipping'))
                        ->schema([
                            InfolistGrid::make(2)->schema([
                                TextEntry::make('shipping_name')
                                    ->label(__('Name')),
                                TextEntry::make('shipping_phone')
                                    ->label(__('Phone')),
                                TextEntry::make('customer.email')
                                    ->label(__('Email'))
                                    ->placeholder('—'),
                                TextEntry::make('shipping_address')
                                    ->label(__('Address'))
                                    ->columnSpanFull(),
                                TextEntry::make('shippingDistrict.name')
                                    ->label(__('District')),
                                TextEntry::make('shippingArea.name')
                                    ->label(__('Area')),
                                TextEntry::make('bkash_trxid')
                                    ->label(__('bKash TrxID'))
                                    ->placeholder('—')
                                    ->visible(fn (Order $record) => $record->isBkash()),
                                TextEntry::make('bkash_sender')
                                    ->label(__('bKash Sender'))
                                    ->placeholder('—')
                                    ->visible(fn (Order $record) => $record->isBkash()),
                                TextEntry::make('bkash_amount')
                                    ->label(__('bKash Amount'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                                    ->visible(fn (Order $record) => $record->isBkash()),
                            ]),
                        ]),

                    InfolistSection::make(__('Order Items'))
                        ->schema([
                            RepeatableEntry::make('items')
                                ->label(__('Items'))
                                ->schema([
                                    InfolistGrid::make(3)->schema([
                                        TextEntry::make('product_name')
                                            ->label(__('global.product')),
                                        TextEntry::make('variant_size')
                                            ->label(__('Size'))
                                            ->formatStateUsing(fn ($state) => "Size: {$state}"),
                                        TextEntry::make('variant_color')
                                            ->label(__('Color')),
                                        TextEntry::make('price')
                                            ->label(__('Unit Price'))
                                            ->formatStateUsing(fn (int $state) => '৳'.number_format($state)),
                                        TextEntry::make('qty')
                                            ->label(__('Qty'))
                                            ->alignCenter(),
                                        TextEntry::make('line_total')
                                            ->label(__('Total'))
                                            ->formatStateUsing(fn (int $state) => '৳'.number_format($state)),
                                    ]),
                                ])
                                ->columns(1),
                        ]),

                    InfolistSection::make(__('Order Summary'))
                        ->schema([
                            InfolistGrid::make(2)->schema([
                                TextEntry::make('subtotal')
                                    ->label(__('Subtotal'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state)),
                                TextEntry::make('discount')
                                    ->label(__('Discount'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                                    ->color('success'),
                                TextEntry::make('delivery_charge')
                                    ->label(__('Delivery'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state)),
                                TextEntry::make('total')
                                    ->label(__('Total'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state))
                                    ->weight('bold')
                                    ->size('lg'),
                            ]),
                        ]),

                    InfolistSection::make(__('Payment Details'))
                        ->visible(fn (Order $record) => $record->isBkash())
                        ->schema([
                            InfolistGrid::make(2)->schema([
                                TextEntry::make('bkash_trxid')
                                    ->label(__('TrxID')),
                                TextEntry::make('bkash_sender')
                                    ->label(__('Sender Number')),
                                TextEntry::make('bkash_amount')
                                    ->label(__('Amount Sent'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state)),
                                TextEntry::make('payment.verified_at')
                                    ->label(__('Verified At'))
                                    ->dateTime('M d, Y H:i')
                                    ->placeholder('—'),
                            ]),
                        ]),

                    InfolistSection::make(__('Timeline'))
                        ->schema([
                            RepeatableEntry::make('statusHistory')
                                ->label(__('Status History'))
                                ->schema([
                                    InfolistGrid::make(3)->schema([
                                        TextEntry::make('to_status')
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
                                            ->formatStateUsing(fn (OrderStatus $state) => $state->label()),
                                        TextEntry::make('created_at')
                                            ->label(__('Date'))
                                            ->dateTime('M d, Y H:i'),
                                        TextEntry::make('changedBy.name')
                                            ->label(__('By'))
                                            ->placeholder('System'),
                                        TextEntry::make('note')
                                            ->label(__('Note'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                                ])
                                ->columns(1),
                        ]),
                ])
                    ->columnSpan(3),
            ])
            ->columns(4);
    }
}
