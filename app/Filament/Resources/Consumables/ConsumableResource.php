<?php

namespace App\Filament\Resources\Consumables;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\Consumables\Pages\CreateConsumable;
use App\Filament\Resources\Consumables\Pages\EditConsumable;
use App\Filament\Resources\Consumables\Pages\ListConsumables;
use App\Filament\Resources\Consumables\Pages\ViewConsumable;
use App\Filament\Resources\RawMaterials\Schemas\RawMaterialForm;
use App\Filament\Resources\RawMaterials\Schemas\RawMaterialInfolist;
use App\Filament\Resources\RawMaterials\Tables\RawMaterialsTable;
use App\Models\RawMaterial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/**
 * Materia prima de tipo CONSUMIBLE (bobinas, empaques y demás insumos que no
 * son grano). Comparte modelo, formulario y tabla con RawMaterialResource;
 * se distinguen por la columna `type`.
 */
class ConsumableResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = RawMaterial::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Materiales Consumibles';

    protected static ?string $modelLabel = 'Material Consumible';

    protected static ?string $pluralModelLabel = 'Materiales Consumibles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    // Sacos=1, Consumibles=2 (Materia Prima) | Terminada=3, Suelta=4 (Producto Terminado)

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', RawMaterial::TYPE_CONSUMABLE);
    }

    public static function form(Schema $schema): Schema
    {
        return RawMaterialForm::configure($schema, RawMaterial::TYPE_CONSUMABLE);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RawMaterialInfolist::configure($schema, RawMaterial::TYPE_CONSUMABLE);
    }

    public static function table(Table $table): Table
    {
        return RawMaterialsTable::configure($table, RawMaterial::TYPE_CONSUMABLE);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsumables::route('/'),
            'create' => CreateConsumable::route('/create'),
            'view' => ViewConsumable::route('/{record}'),
            'edit' => EditConsumable::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('type', RawMaterial::TYPE_CONSUMABLE)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
