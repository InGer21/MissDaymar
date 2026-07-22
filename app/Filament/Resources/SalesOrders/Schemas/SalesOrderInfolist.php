<?php

namespace App\Filament\Resources\SalesOrders\Schemas;

use App\Models\SalesOrder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalesOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('id')
                    ->label('#'),
                TextEntry::make('entity.name')
                    ->label('Cliente'),
                TextEntry::make('user.name')
                    ->label('Creado por'),
                TextEntry::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'under_review' => 'En Revisión',
                        'invoicing' => 'En Facturación',
                        'invoiced' => 'Facturado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    }),
                TextEntry::make('notes')
                    ->label('Notas')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('total_usd')
                    ->label('Total ($)')
                    ->numeric(),
                TextEntry::make('profit_doc_num')
                    ->label('N° Doc. Profit')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (SalesOrder $record): bool => $record->trashed()),
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
