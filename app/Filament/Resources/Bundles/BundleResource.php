<?php

namespace App\Filament\Resources\Bundles;

use App\Filament\Resources\Bundles\Pages\CreateBundle;
use App\Filament\Resources\Bundles\Pages\EditBundle;
use App\Filament\Resources\Bundles\Pages\ListBundles;
use App\Filament\Resources\Bundles\Pages\ViewBundle;
use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\ProductPresentations\Schemas\ProductPresentationForm;
use App\Filament\Resources\ProductPresentations\Schemas\ProductPresentationInfolist;
use App\Filament\Resources\ProductPresentations\Tables\ProductPresentationsTable;
use App\Models\Product;
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
 * Producto Terminado — BULTOS (bulto y medio bulto). El multi-empaque que
 * sale del reenvasado de un saco.
 */
class BundleResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = ProductPresentation::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista', 'vendedor'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Mercancía Terminada';

    protected static ?string $modelLabel = 'Bulto';

    protected static ?string $pluralModelLabel = 'Mercancía Terminada';

    protected static ?string $recordTitleAttribute = 'format';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('presentation_type', ProductPresentation::TYPES_BUNDLE)
            ->whereHas('product.category', fn (Builder $q) => $q->whereNotIn('slug', Product::HIDDEN_CATEGORY_SLUGS));
    }

    public static function form(Schema $schema): Schema
    {
        return ProductPresentationForm::configure($schema, ProductPresentation::TYPES_BUNDLE);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductPresentationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductPresentationsTable::configure($table, ProductPresentation::TYPES_BUNDLE);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBundles::route('/'),
            'create' => CreateBundle::route('/create'),
            'view' => ViewBundle::route('/{record}'),
            'edit' => EditBundle::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->whereIn('presentation_type', ProductPresentation::TYPES_BUNDLE)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
