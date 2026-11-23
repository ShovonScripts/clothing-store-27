<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label(__('Email'))
                        ->email()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                        ->label(__('Phone'))
                        ->tel()
                        ->maxLength(11)
                        ->placeholder('01XXXXXXXXX')
                        ->unique(ignoreRecord: true),

                    TextInput::make('default_address')
                        ->label(__('Default Address'))
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Select::make('district_id')
                        ->label(__('District'))
                        ->relationship('district', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder(__('Select district')),

                    TextInput::make('area')
                        ->label(__('Area'))
                        ->maxLength(100),

                    Toggle::make('is_blacklisted')
                        ->label(__('Blacklisted')),

                    Textarea::make('blacklist_reason')
                        ->label(__('Blacklist Reason'))
                        ->rows(3)
                        ->visible(fn (callable $get) => $get('is_blacklisted')),

                    TextInput::make('pending_orders_today')
                        ->label(__('Pending Orders Today'))
                        ->numeric()
                        ->default(0)
                        ->disabled(),

                    TextInput::make('total_orders')
                        ->label(__('Total Orders'))
                        ->numeric()
                        ->default(0)
                        ->disabled(),

                    TextInput::make('delivered_orders')
                        ->label(__('Delivered Orders'))
                        ->numeric()
                        ->default(0)
                        ->disabled(),

                    TextInput::make('cancelled_orders')
                        ->label(__('Cancelled Orders'))
                        ->numeric()
                        ->default(0)
                        ->disabled(),

                    TextInput::make('total_spent')
                        ->label(__('Total Spent'))
                        ->numeric()
                        ->default(0)
                        ->disabled(),

                    DatePicker::make('last_order_date')
                        ->label(__('Last Order Date'))
                        ->disabled(),

                    Textarea::make('notes')
                        ->label(__('Notes'))
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
