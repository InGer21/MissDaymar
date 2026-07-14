<?php

namespace App\Filament\Resources\Conversions\Schemas;

use App\Models\Conversion;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ConversionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('#'),
                TextEntry::make('notes')
                    ->label('Notas')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (Conversion $record): bool => $record->trashed()),
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
