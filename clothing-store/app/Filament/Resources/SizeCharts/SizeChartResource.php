<?php

declare(strict_types=1);

namespace App\Filament\Resources\SizeCharts;

use App\Enums\NavigationGroup;
use App\Filament\Resources\SizeCharts\Pages\CreateSizeChart;
use App\Filament\Resources\SizeCharts\Pages\EditSizeChart;
use App\Filament\Resources\SizeCharts\Pages\ListSizeCharts;
use App\Filament\Resources\SizeCharts\Schemas\SizeChartForm;
use App\Filament\Resources\SizeCharts\Tables\SizeChartsTable;
use App\Models\SizeChart;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SizeChartResource extends Resource
{
    protected static ?string $model = SizeChart::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::Catalog;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('Size Charts');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Size Charts');
    }

    public static function form(Schema $schema): Schema
    {
        return SizeChartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SizeChartsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSizeCharts::route('/'),
            'create' => CreateSizeChart::route('/create'),
            'edit' => EditSizeChart::route('/{record}/edit'),
        ];
    }
}
