<?php

declare(strict_types=1);

namespace App\Filament\Resources\SizeCharts\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SizeChartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('e.g., T-Shirt / Polo (inches)')),
                    ])->columns(1),

                Section::make(__('Size Chart Data'))
                    ->description(__('Enter columns and rows as JSON. Example: {"columns":["Size","Chest","Length"],"rows":[["M","38","27"],["L","40","28"]]}'))
                    ->schema([
                        KeyValue::make('table_json')
                            ->label(__('Columns & Rows'))
                            ->keyLabel(__('Column'))
                            ->valueLabel(__('Value'))
                            ->addActionLabel(__('Add Column'))
                            ->deleteActionLabel(__('Delete'))
                            ->required(),
                    ])->columns(1),

                Section::make(__('Notes'))
                    ->schema([
                        Textarea::make('note')
                            ->label(__('Additional Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }
}
