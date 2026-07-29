<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use App\Models\RawMaterial;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RawMaterialInfolist
{
    public static function configure(Schema $schema, string $type = RawMaterial::TYPE_GRAIN): Schema
    {
        $isGrain = $type === RawMaterial::TYPE_GRAIN;

        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('sku')
                    ->label('SKU')
                    ->placeholder('—'),
                TextEntry::make('code')
                    ->label('Código interno'),
                TextEntry::make('name')
                    ->label($isGrain ? 'Nombre del grano' : 'Nombre del paquete'),
                TextEntry::make('purchase_presentation')
                    ->label($isGrain ? 'Presentación de compra' : 'Capacidad del paquete')
                    ->placeholder('—'),
                TextEntry::make('current_stock')
                    ->label($isGrain ? 'Sacos en existencia' : 'Bobinas en existencia')
                    ->numeric(decimalPlaces: 0),
                TextEntry::make('kg_per_unit')
                    ->label('KG por saco')
                    ->numeric(decimalPlaces: 3)
                    ->placeholder('—')
                    ->visible($isGrain),
                TextEntry::make('total_kg')
                    ->label('KG de grano en existencia')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('—')
                    ->visible($isGrain),
                TextEntry::make('unit')
                    ->label('Unidad'),
                TextEntry::make('unit_cost')
                    ->label('Costo Unitario ($)')
                    ->numeric()
                    ->placeholder('—'),
                TextEntry::make('notes')
                    ->label('Notas')
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (RawMaterial $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->placeholder('—'),
                TextEntry::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime()
                    ->placeholder('—'),
            ]);
    }
}
