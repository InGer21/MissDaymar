<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('presentation.product.name')
                    ->label('Producto'),
                TextEntry::make('presentation.format')
                    ->label('Presentación'),
                TextEntry::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entry' => 'Entrada',
                        'exit' => 'Salida',
                        'adjustment' => 'Ajuste',
                        default => $state,
                    }),
                TextEntry::make('quantity')
                    ->label('Cantidad')
                    ->numeric(),
                TextEntry::make('notes')
                    ->label('Notas')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
