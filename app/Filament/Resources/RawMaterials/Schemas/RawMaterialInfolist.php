<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use App\Models\RawMaterial;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RawMaterialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('code')
                    ->label('Código'),
                TextEntry::make('name')
                    ->label('Nombre'),
                TextEntry::make('product.name')
                    ->label('Producto Asociado')
                    ->placeholder('-'),
                TextEntry::make('purchase_presentation')
                    ->label('Presentación de Compra'),
                TextEntry::make('unit')
                    ->label('Unidad'),
                TextEntry::make('unit_cost')
                    ->label('Costo Unitario ($)')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stock')
                    ->label('Stock Actual')
                    ->numeric(),
                TextEntry::make('notes')
                    ->label('Notas')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (RawMaterial $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
