<?php

namespace App\Filament\Resources\RawMaterials;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\RawMaterials\Pages\CreateRawMaterial;
use App\Filament\Resources\RawMaterials\Pages\EditRawMaterial;
use App\Filament\Resources\RawMaterials\Pages\ListRawMaterials;
use App\Filament\Resources\RawMaterials\Pages\ViewRawMaterial;
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

class RawMaterialResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = RawMaterial::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Materia Prima';

    protected static ?string $pluralModelLabel = 'Materias Primas';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RawMaterialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RawMaterialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RawMaterialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRawMaterials::route('/'),
            'create' => CreateRawMaterial::route('/create'),
            'view' => ViewRawMaterial::route('/{record}'),
            'edit' => EditRawMaterial::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
