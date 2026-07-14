<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('presentation.product.name')
                    ->label('Producto'),
                TextColumn::make('presentation.format')
                    ->label('Presentación'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entry' => 'success',
                        'exit' => 'danger',
                        'adjustment' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entry' => 'Entrada',
                        'exit' => 'Salida',
                        'adjustment' => 'Ajuste',
                    }),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
