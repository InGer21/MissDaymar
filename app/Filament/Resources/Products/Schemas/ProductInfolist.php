<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Nombre'),
                TextEntry::make('category.name')
                    ->label('Categoría')
                    ->placeholder('-'),
                TextEntry::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'MP' => 'Materia Prima',
                        'PT' => 'Producto Terminado',
                        'Ambos' => 'Ambos',
                        'Puro' => 'Producto Puro',
                        default => $state,
                    }),
                TextEntry::make('line_1')
                    ->label('Línea 1')
                    ->placeholder('-'),
                TextEntry::make('line_2')
                    ->label('Línea 2')
                    ->placeholder('-'),
                IconEntry::make('is_pure')
                    ->label('¿Puro?')
                    ->boolean(),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (Product $record): bool => $record->trashed()),
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
