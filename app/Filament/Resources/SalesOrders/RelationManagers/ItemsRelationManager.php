<?php

namespace App\Filament\Resources\SalesOrders\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items de la Orden';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('presentation_id')
                    ->label('Presentación')
                    ->relationship('presentation', 'format')
                    ->searchable()
                    ->required(),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_price_usd')
                    ->label('Precio Unitario ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('presentation.product.name')
                    ->label('Producto'),
                TextColumn::make('presentation.format')
                    ->label('Presentación'),
                TextColumn::make('quantity')
                    ->label('Cant.')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price_usd')
                    ->label('Precio Unit. ($)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
