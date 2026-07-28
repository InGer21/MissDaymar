<?php

namespace App\Filament\Resources\ReceivableOrders;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\ReceivableOrders\Pages\ListReceivableOrders;
use App\Filament\Resources\ReceivableOrders\Pages\ViewReceivableOrder;
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
 * Pedidos ya despachados cuya factura sigue sin cobrarse. Es una vista de
 * seguimiento: se marca el cobro desde la factura, no desde aquí.
 *
 * Un pedido despachado sin factura no aparece: si no hay factura, todavía no
 * hay nada que cobrar.
 */
class ReceivableOrderResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = SalesOrder::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'vendedor', 'facturacion'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Despachados por Cobrar';

    protected static ?string $modelLabel = 'Pedido por Cobrar';

    protected static ?string $pluralModelLabel = 'Pedidos Despachados por Cobrar';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('dispatched_at')
            ->whereHas('invoice', fn (Builder $q) => $q->whereNull('paid_at'));
    }

    /** Contador en el menú: cuántos pedidos están pendientes de cobro. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceivableOrders::route('/'),
            'view' => ViewReceivableOrder::route('/{record}'),
        ];
    }
}
