<?php

namespace App\Filament\Resources\Entities;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\Entities\Pages\CreateEntity;
use App\Filament\Resources\Entities\Pages\EditEntity;
use App\Filament\Resources\Entities\Pages\ListEntities;
use App\Filament\Resources\Entities\Pages\ViewEntity;
use App\Filament\Resources\Entities\Schemas\EntityForm;
use App\Filament\Resources\Entities\Schemas\EntityInfolist;
use App\Filament\Resources\Entities\Tables\EntitiesTable;
use App\Models\Entity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class EntityResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Entity::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'vendedor', 'facturacion'],
            'create' => ['admin', 'vendedor'],
            'edit' => ['admin', 'vendedor'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Ventas';

    protected static ?string $modelLabel = 'Entidad';

    protected static ?string $pluralModelLabel = 'Entidades';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EntityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EntityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntities::route('/'),
            'create' => CreateEntity::route('/create'),
            'view' => ViewEntity::route('/{record}'),
            'edit' => EditEntity::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $user = auth()->user();

        if ($user && $user->role === 'vendedor') {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }
}
