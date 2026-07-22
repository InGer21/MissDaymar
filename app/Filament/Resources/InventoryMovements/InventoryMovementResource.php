<?php

namespace App\Filament\Resources\InventoryMovements;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\InventoryMovements\Pages\CreateInventoryMovement;
use App\Filament\Resources\InventoryMovements\Pages\EditInventoryMovement;
use App\Filament\Resources\InventoryMovements\Pages\ListInventoryMovements;
use App\Filament\Resources\InventoryMovements\Pages\ViewInventoryMovement;
use App\Filament\Resources\InventoryMovements\Schemas\InventoryMovementForm;
use App\Filament\Resources\InventoryMovements\Schemas\InventoryMovementInfolist;
use App\Filament\Resources\InventoryMovements\Tables\InventoryMovementsTable;
use App\Models\InventoryMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InventoryMovementResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = InventoryMovement::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Movimiento de Inventario';

    protected static ?string $pluralModelLabel = 'Movimientos de Inventario';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('presentation.product');
    }

    public static function form(Schema $schema): Schema
    {
        return InventoryMovementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventoryMovementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryMovementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryMovements::route('/'),
            'create' => CreateInventoryMovement::route('/create'),
            'view' => ViewInventoryMovement::route('/{record}'),
            'edit' => EditInventoryMovement::route('/{record}/edit'),
        ];
    }
}
