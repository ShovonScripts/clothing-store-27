<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Schema $schema): Schema
    {
        return Schema::make()
            ->components([
                Split::make([
                    Section::make(__('User Information'))
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label(__('Name')),
                                TextEntry::make('email')
                                    ->label(__('Email')),
                                TextEntry::make('phone')
                                    ->label(__('Phone'))
                                    ->placeholder('—'),
                                IconEntry::make('is_active')
                                    ->label(__('Active'))
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                TextEntry::make('created_at')
                                    ->label(__('Created'))
                                    ->dateTime('M d, Y H:i'),
                                TextEntry::make('last_login_at')
                                    ->label(__('Last Login'))
                                    ->dateTime('M d, Y H:i')
                                    ->placeholder('—'),
                            ]),
                        ]),

                    Section::make(__('Roles'))
                        ->schema([
                            RepeatableEntry::make('roles')
                                ->label(__('Roles'))
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('name')
                                            ->label(__('Role Name'))
                                            ->formatStateUsing(fn (string $state) => ucfirst($state))
                                            ->badge()
                                            ->colors([
                                                'danger' => 'owner',
                                                'warning' => 'manager',
                                                'info' => 'staff',
                                            ]),
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
