<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Customer;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    public function infolist(Schema $schema): Schema
    {
        return Schema::make()
            ->components([
                Split::make([
                    Section::make(__('Customer Information'))
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label(__('Name')),
                                TextEntry::make('email')
                                    ->label(__('Email'))
                                    ->placeholder('—'),
                                TextEntry::make('phone')
                                    ->label(__('Phone')),
                                IconEntry::make('is_blacklisted')
                                    ->label(__('Blacklisted'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-ban')
                                    ->falseIcon('heroicon-o-check-circle')
                                    ->trueColor('danger')
                                    ->falseColor('success'),
                                TextEntry::make('blacklist_reason')
                                    ->label(__('Blacklist Reason'))
                                    ->placeholder('—')
                                    ->visible(fn (Customer $record) => $record->is_blacklisted),
                                TextEntry::make('created_at')
                                    ->label(__('Created'))
                                    ->dateTime('M d, Y H:i'),
                            ]),
                        ]),

                    Section::make(__('Address'))
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('default_address')
                                    ->label(__('Default Address'))
                                    ->columnSpanFull(),
                                TextEntry::make('district.name')
                                    ->label(__('District')),
                                TextEntry::make('area')
                                    ->label(__('Area')),
                            ]),
                        ]),

                    Section::make(__('Order Statistics'))
                        ->schema([
                            Grid::make(4)->schema([
                                TextEntry::make('total_orders')
                                    ->label(__('Total Orders'))
                                    ->numeric(),
                                TextEntry::make('delivered_orders')
                                    ->label(__('Delivered'))
                                    ->numeric(),
                                TextEntry::make('cancelled_orders')
                                    ->label(__('Cancelled'))
                                    ->numeric(),
                                TextEntry::make('total_spent')
                                    ->label(__('Total Spent'))
                                    ->formatStateUsing(fn (int $state) => '৳'.number_format($state)),
                            ]),
                        ]),

                    Section::make(__('Recent Activity'))
                        ->schema([
                            TextEntry::make('last_order_date')
                                ->label(__('Last Order Date'))
                                ->date('M d, Y')
                                ->placeholder('—'),
                            TextEntry::make('pending_orders_today')
                                ->label(__('Pending Orders Today'))
                                ->numeric(),
                            TextEntry::make('notes')
                                ->label(__('Notes'))
                                ->placeholder('—')
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpan(3),
            ])
            ->columns(4);
    }
}
