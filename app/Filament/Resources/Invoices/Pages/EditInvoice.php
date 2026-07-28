<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Invoice $record */
        $record = $this->getRecord();

        return [
            ViewAction::make(),
            Action::make('mark_paid')
                ->label('Marcar como Cobrada')
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->visible(! $record->isPaid())
                ->requiresConfirmation()
                ->modalHeading('Marcar factura como cobrada')
                ->modalDescription('Confirma que ya se recibió el pago de esta factura.')
                ->action(function () use ($record) {
                    $record->update(['paid_at' => now()]);

                    Notification::make()
                        ->title('Factura marcada como cobrada')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),
            Action::make('mark_unpaid')
                ->label('Marcar como Por Cobrar')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->visible($record->isPaid())
                ->requiresConfirmation()
                ->action(function () use ($record) {
                    $record->update(['paid_at' => null]);

                    Notification::make()
                        ->title('Factura marcada como por cobrar')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
