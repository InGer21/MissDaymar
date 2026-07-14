<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nombre'),
                TextEntry::make('slug')
                    ->label('Slug'),
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
