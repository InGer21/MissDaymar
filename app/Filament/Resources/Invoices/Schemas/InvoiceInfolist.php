<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('invoice_number')
                    ->label('N° Factura'),
                TextEntry::make('salesOrder.entity.name')
                    ->label('Cliente'),
                TextEntry::make('salesOrder.id')
                    ->label('Orden de Venta'),
                TextEntry::make('bcv_rate')
                    ->label('Tasa BCV ($)')
                    ->numeric(),
                TextEntry::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->numeric(),
                TextEntry::make('igtf_usd')
                    ->label('IGTF ($)')
                    ->numeric(),
                TextEntry::make('total_usd')
                    ->label('Total ($)')
                    ->numeric(),
                TextEntry::make('issued_at')
                    ->label('Fecha de Emisión')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->label('Eliminada')
                    ->dateTime()
                    ->visible(fn (Invoice $record): bool => $record->trashed()),
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
