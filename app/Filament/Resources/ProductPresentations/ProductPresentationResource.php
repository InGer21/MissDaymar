<?php

namespace App\Filament\Resources\ProductPresentations;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\ProductPresentations\Pages\CreateProductPresentation;
use App\Filament\Resources\ProductPresentations\Pages\EditProductPresentation;
use App\Filament\Resources\ProductPresentations\Pages\ListProductPresentations;
use App\Filament\Resources\ProductPresentations\Pages\ViewProductPresentation;
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
 * Producto Terminado — SACOS. Comparte modelo y formularios con
 * BundleResource y LooseGoodResource; se distinguen por `presentation_type`.
 */
class ProductPresentationResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = ProductPresentation::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista', 'vendedor'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Sacos';

    protected static ?string $modelLabel = 'Saco';

    protected static ?string $pluralModelLabel = 'Sacos';

    protected static ?string $recordTitleAttribute = 'format';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('presentation_type', ProductPresentation::TYPES_SACK);
    }

    public static function form(Schema $schema): Schema
    {
        return ProductPresentationForm::configure($schema, ProductPresentation::TYPES_SACK);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductPresentationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductPresentationsTable::configure($table, ProductPresentation::TYPES_SACK);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductPresentations::route('/'),
            'create' => CreateProductPresentation::route('/create'),
            'view' => ViewProductPresentation::route('/{record}'),
            'edit' => EditProductPresentation::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->whereIn('presentation_type', ProductPresentation::TYPES_SACK)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
