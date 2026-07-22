<?php

namespace App\Filament\Resources\Conversions\RelationManagers;

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

    protected static ?string $title = 'Items de Conversión';

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
                Select::make('type')
                    ->label('Tipo')
                    ->required()
                    ->options([
                        'input' => 'Insumo',
                        'output' => 'Producto Generado',
                        'sobrante' => 'Sobrante',
                        'merma' => 'Merma',
                    ]),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(0.001),
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
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'input' => 'info',
                        'output' => 'success',
                        'sobrante' => 'warning',
                        'merma' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'input' => 'Insumo',
                        'output' => 'Producto Generado',
                        'sobrante' => 'Sobrante',
                        'merma' => 'Merma',
                    }),
                TextColumn::make('quantity')
                    ->label('Cantidad')
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
                ->with('presentation.product')
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
