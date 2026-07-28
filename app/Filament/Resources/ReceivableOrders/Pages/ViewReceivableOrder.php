<?php

namespace App\Filament\Resources\ReceivableOrders\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\ReceivableOrders\ReceivableOrderResource;
use App\Models\SalesOrder;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewReceivableOrder extends ViewRecord
{
    protected static string $resource = ReceivableOrderResource::class;

    protected function getHeaderActions(): array
    {
        /** @var SalesOrder $record */
        $record = $this->getRecord();

        return [
            // El cobro se registra sobre la factura, que es donde vive `paid_at`.
            Action::make('go_to_invoice')
                ->label('Ir a la Factura')
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->visible(fn () => $record->invoice !== null)
                ->url(fn () => $record->invoice
                    ? InvoiceResource::getUrl('edit', ['record' => $record->invoice])
                    : null),
        ];
    }
}
