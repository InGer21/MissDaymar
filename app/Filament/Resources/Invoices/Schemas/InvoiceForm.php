<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('sales_order_id')
                    ->label('Orden de Venta')
                    ->relationship('salesOrder', 'id')
                    ->required(),
                TextInput::make('invoice_number')
                    ->label('Número de Factura')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                TextInput::make('bcv_rate')
                    ->label('Tasa BCV ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.0001),
                TextInput::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('igtf_usd')
                    ->label('IGTF ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->default(0),
                TextInput::make('total_usd')
                    ->label('Total ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                DateTimePicker::make('issued_at')
                    ->label('Fecha de Emisión')
                    ->required(),
            ]);
    }
}
