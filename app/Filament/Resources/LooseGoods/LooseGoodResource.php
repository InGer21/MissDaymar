<?php

namespace App\Filament\Resources\LooseGoods;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\LooseGoods\Pages\CreateLooseGood;
use App\Filament\Resources\LooseGoods\Pages\EditLooseGood;
use App\Filament\Resources\LooseGoods\Pages\ListLooseGoods;
use App\Filament\Resources\LooseGoods\Pages\ViewLooseGood;
use App\Filament\Resources\ProductPresentations\Schemas\ProductPresentationForm;
use App\Filament\Resources\ProductPresentations\Schemas\ProductPresentationInfolist;
use App\Filament\Resources\ProductPresentations\Tables\ProductPresentationsTable;
use App\Models\ProductPresentation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/**
 * Producto Terminado — MERCANCÍA SUELTA (bolsa individual, bolsa 4kg, ristra
 * y por kilo). Lo que sobra cuando no se completa un bulto, más las
 * presentaciones que se venden por unidad.
 */
class LooseGoodResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = ProductPresentation::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista', 'vendedor'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Mercancía Suelta';

    protected static ?string $modelLabel = 'Mercancía Suelta';

    protected static ?string $pluralModelLabel = 'Mercancía Suelta';

    protected static ?string $recordTitleAttribute = 'format';

    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('presentation_type', ProductPresentation::TYPES_LOOSE);
    }

    public static function form(Schema $schema): Schema
    {
        return ProductPresentationForm::configure($schema, ProductPresentation::TYPES_LOOSE);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductPresentationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductPresentationsTable::configure($table, ProductPresentation::TYPES_LOOSE);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLooseGoods::route('/'),
            'create' => CreateLooseGood::route('/create'),
            'view' => ViewLooseGood::route('/{record}'),
            'edit' => EditLooseGood::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->whereIn('presentation_type', ProductPresentation::TYPES_LOOSE)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
