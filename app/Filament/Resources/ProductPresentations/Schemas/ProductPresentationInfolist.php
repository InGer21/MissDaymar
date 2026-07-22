<?php

namespace App\Filament\Resources\ProductPresentations\Schemas;

use App\Models\ProductPresentation;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductPresentationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('product.name')
                    ->label('Producto'),
                TextEntry::make('presentation_type')
                    ->label('Tipo'),
                TextEntry::make('format')
                    ->label('Formato'),
                TextEntry::make('unit')
                    ->label('Unidad'),
                TextEntry::make('current_stock')
                    ->label('Stock Actual')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->label('¿Activa?')
                    ->boolean(),
                TextEntry::make('profit_unit_code')
                    ->label('Cód. Unidad Profit')
                    ->placeholder('-'),
                TextEntry::make('profit_equivalence')
                    ->label('Equivalencia Profit')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_main_unit')
                    ->label('¿Unidad Principal?')
                    ->boolean(),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (ProductPresentation $record): bool => $record->trashed()),
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
