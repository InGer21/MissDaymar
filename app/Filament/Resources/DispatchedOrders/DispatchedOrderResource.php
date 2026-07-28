<?php

namespace App\Filament\Resources\DispatchedOrders;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\DispatchedOrders\Pages\EditDispatchedOrder;
use App\Filament\Resources\DispatchedOrders\Pages\ListDispatchedOrders;
use App\Filament\Resources\DispatchedOrders\Pages\ViewDispatchedOrder;
use App\Filament\Resources\SalesOrders\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\SalesOrders\Schemas\SalesOrderForm;
use App\Filament\Resources\SalesOrders\Schemas\SalesOrderInfolist;
use App\Filament\Resources\SalesOrders\Tables\SalesOrdersTable;
use App\Models\SalesOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Pedidos que ya salieron del almacén. No se crean aquí: un pedido llega a
 * esta sección al marcarse como despachado desde Pedidos Generados.
 *
 * Ojo: a diferencia de Granos/Clientes/etc., aquí NO se restringe el acceso
 * por URL (`getRecordRouteBindingEloquentQuery`), porque un mismo pedido
 * cambia de sección con el tiempo y hacerlo rompería enlaces y redirecciones.
 */
class DispatchedOrderResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = SalesOrder::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'vendedor', 'facturacion'],
            'edit' => ['admin', 'vendedor'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Pedidos Despachados';

    protected static ?string $modelLabel = 'Pedido Despachado';

    protected static ?string $pluralModelLabel = 'Pedidos Despachados';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('dispatched_at');
    }

    public static function form(Schema $schema): Schema
    {
        return SalesOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesOrdersTable::configure($table, showDispatch: true, showPayment: true);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDispatchedOrders::route('/'),
            'view' => ViewDispatchedOrder::route('/{record}'),
            'edit' => EditDispatchedOrder::route('/{record}/edit'),
        ];
    }
}
