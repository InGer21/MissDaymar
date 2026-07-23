<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MP' => 'gray',
                        'PT' => 'success',
                        'Ambos' => 'warning',
                        'Puro' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'MP' => 'Materia Prima',
                        'PT' => 'Producto Terminado',
                        'Ambos' => 'Ambos',
                        'Puro' => 'Producto Puro',
                    }),
                TextColumn::make('total_stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->tooltip(fn ($state) => $state > 0 ? "{$state} disponibles" : ($state < 0 ? 'Stock negativo' : 'Sin stock')),
                TextColumn::make('line_1')
                    ->label('Línea 1')
                    ->searchable(),
                TextColumn::make('line_2')
                    ->label('Línea 2')
                    ->searchable(),
                IconColumn::make('is_pure')
                    ->label('¿Puro?')
                    ->boolean(),
                TextColumn::make('profit_code')
                    ->label('Cód. Profit')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_service')
                    ->label('¿Servicio?')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'MP' => 'Materia Prima',
                        'PT' => 'Producto Terminado',
                        'Ambos' => 'Ambos',
                        'Puro' => 'Producto Puro',
                    ]),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
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
