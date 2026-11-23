<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label(__('Email'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                        ->label(__('Phone'))
                        ->tel()
                        ->maxLength(11)
                        ->placeholder('01XXXXXXXXX')
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label(__('Password'))
                        ->password()
                        ->revealable()
                        ->required(fn (string $context) => $context === 'create')
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->maxLength(255)
                        ->autocomplete('new-password'),

                    Select::make('roles')
                        ->label(__('Roles'))
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->optionsLimit(10)
                        ->getOptionLabelFromRecordUsing(fn (Role $role) => $role->name),

                    Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),
                ]),
            ]);
    }
}
