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
     * Compartida por Sacos (grano) y Materiales Consumibles (bobinas).
     * Cada una muestra las columnas que le corresponden a su realidad física.
     */
    public static function configure(Table $table, string $type = RawMaterial::TYPE_GRAIN): Table
    {
        $isGrain = $type === RawMaterial::TYPE_GRAIN;

        // OJO: no repetir `TextColumn::make('current_stock')` dos veces con
        // distinta visibilidad — Filament indexa las columnas por nombre y la
        // segunda pisa a la primera, dejando la columna invisible.
        $existencia = TextColumn::make('current_stock')
            ->label($isGrain ? 'Sacos en existencia' : 'Bobinas en existencia')
            ->numeric(decimalPlaces: 0)
            ->sortable()
            ->color(fn ($state) => $state > 0 ? 'success' : 'gray');

        $columns = [
            TextColumn::make('sku')
                ->label('SKU')
                ->placeholder('—')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label($isGrain ? 'Nombre del grano' : 'Nombre del paquete')
                ->searchable()
                ->sortable(),
        ];

        if ($isGrain) {
            $columns[] = $existencia;
            $columns[] = TextColumn::make('total_kg')
                ->label('KG de grano')
                // Sin peso por saco cargado no se puede calcular: se muestra
                // "—" en vez de un 0 que se leería como "no hay grano".
                ->placeholder('—')
                ->numeric(decimalPlaces: 2);
        } else {
            $columns[] = TextColumn::make('purchase_presentation')
                ->label('Capacidad del paquete')
                ->placeholder('—');
            $columns[] = $existencia;
        }

        $columns[] = TextColumn::make('unit_cost')
            ->label('Costo ($)')
            ->money()
            ->placeholder('—')
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        return $table
            ->columns($columns)
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
