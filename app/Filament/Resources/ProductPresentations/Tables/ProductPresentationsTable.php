<?php

namespace App\Filament\Resources\ProductPresentations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductPresentationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable(),
                TextColumn::make('presentation_type')
                    ->label('Tipo'),
                TextColumn::make('format')
                    ->label('Formato')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('Unidad'),
                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('¿Activo?')
                    ->boolean(),
            ])
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
