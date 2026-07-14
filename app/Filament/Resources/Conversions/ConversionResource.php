<?php

namespace App\Filament\Resources\Conversions;

use App\Filament\Resources\Concerns\HasRoleAccess;
use App\Filament\Resources\Conversions\Pages\CreateConversion;
use App\Filament\Resources\Conversions\Pages\EditConversion;
use App\Filament\Resources\Conversions\Pages\ListConversions;
use App\Filament\Resources\Conversions\Pages\ViewConversion;
use App\Filament\Resources\Conversions\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Conversions\Schemas\ConversionForm;
use App\Filament\Resources\Conversions\Schemas\ConversionInfolist;
use App\Filament\Resources\Conversions\Tables\ConversionsTable;
use App\Models\Conversion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ConversionResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Conversion::class;

    protected static function getRoleAccess(): array
    {
        return [
            'view' => ['admin', 'almacenista'],
        ];
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Conversión';

    protected static ?string $pluralModelLabel = 'Conversiones';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ConversionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConversionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConversionsTable::configure($table);
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
            'index' => ListConversions::route('/'),
            'create' => CreateConversion::route('/create'),
            'view' => ViewConversion::route('/{record}'),
            'edit' => EditConversion::route('/{record}/edit'),
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
