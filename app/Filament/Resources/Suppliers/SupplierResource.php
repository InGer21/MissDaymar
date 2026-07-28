<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\Entities\Schemas\EntityForm;
use App\Filament\Resources\Entities\Schemas\EntityInfolist;
use App\Filament\Resources\Entities\Tables\EntitiesTable;
use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Suppliers\Pages\ViewSupplier;
use App\Models\Entity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/**
 * Proveedores: quienes surten la materia prima, por eso viven en Inventario
 * y no en Ventas. Comparten modelo y formularios con EntityResource
 * (Clientes); se distinguen por la columna `type`.
 */
class SupplierResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Entity::class;

    /**
     * Solo admin (decisión de Juan, 2026-07-28): aunque Proveedores viva en
     * el módulo de Inventario, el almacenista no obtiene acceso.
     */
    protected static function getRoleAccess(): array
    {
        return [];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Proveedores';

    protected static ?string $modelLabel = 'Proveedor';

    protected static ?string $pluralModelLabel = 'Proveedores';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 7;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', Entity::TYPE_SUPPLIER);
    }

    public static function form(Schema $schema): Schema
    {
        return EntityForm::configure($schema, Entity::TYPE_SUPPLIER);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EntityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntitiesTable::configure($table, Entity::TYPE_SUPPLIER);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'view' => ViewSupplier::route('/{record}'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('type', Entity::TYPE_SUPPLIER)
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
