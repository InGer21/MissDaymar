<?php

namespace App\Filament\Resources\ProductPresentations\Tables;

use App\Models\ProductPresentation;
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

class ProductPresentationsTable
{
    /**
     * Compartida por Mercancía Terminada (bultos) y Mercancía Suelta.
     *
     * @param  list<string>|null  $types
     */
    public static function configure(Table $table, ?array $types = null): Table
    {
        $typeOptions = $types === null
            ? ProductPresentation::TYPE_LABELS
            : array_intersect_key(ProductPresentation::TYPE_LABELS, array_flip($types));

        $isBundle = $types === ProductPresentation::TYPES_BUNDLE;

        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('presentation_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ProductPresentation::TYPE_LABELS[$state] ?? $state)
                    // Con un solo tipo en la sección la columna no aporta nada.
                    ->visible(count($typeOptions) > 1),
                TextColumn::make('current_stock')
                    ->label($isBundle ? 'Bultos (unidades)' : 'Unidades')
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('format')
                    ->label($isBundle ? 'Dimensiones del bulto' : 'Gramos por paquete')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('¿Activo?')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('product.name')
            ->filters([
                SelectFilter::make('presentation_type')
                    ->label('Tipo')
                    ->options($typeOptions)
                    ->visible(count($typeOptions) > 1),
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
