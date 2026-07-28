<?php

namespace App\Filament\Resources\RawMaterials\Tables;

use App\Models\RawMaterial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RawMaterialsTable
{
    /**
     * Compartida entre Granos y Consumibles. Las columnas que solo aplican a
     * granos (producto asociado, stock derivado) se ocultan para consumibles.
     */
    public static function configure(Table $table, string $type = RawMaterial::TYPE_GRAIN): Table
    {
        $isGrain = $type === RawMaterial::TYPE_GRAIN;

        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Producto Asociado')
                    ->placeholder('—')
                    ->visible($isGrain)
                    ->toggleable(),
                TextColumn::make('purchase_presentation')
                    ->label('Presentación de Compra')
                    ->placeholder('—'),
                TextColumn::make('unit')
                    ->label('Unidad'),
                TextColumn::make('unit_cost')
                    ->label('Costo ($)')
                    ->money()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->visible($isGrain)
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
