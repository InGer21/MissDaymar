<?php

namespace App\Filament\Resources\Entities\Schemas;

use App\Models\Entity;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EntityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'customer' => 'Cliente',
                        'supplier' => 'Proveedor',
                        default => $state,
                    }),
                TextEntry::make('name')
                    ->label('Nombre o Razón Social'),
                TextEntry::make('rif')
                    ->label('RIF'),
                TextEntry::make('sunagro')
                    ->label('SUNAGRO')
                    ->placeholder('-'),
                TextEntry::make('fiscal_state')
                    ->label('Estado'),
                TextEntry::make('fiscal_city')
                    ->label('Ciudad'),
                TextEntry::make('address')
                    ->label('Dirección')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->label('Teléfono')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Correo Electrónico')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->label('¿Activo?')
                    ->boolean(),
                TextEntry::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime()
                    ->visible(fn (Entity $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
