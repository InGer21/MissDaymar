<?php

namespace App\Filament\Resources\SalesOrders\Pages;

use App\Filament\Resources\SalesOrders\SalesOrderResource;
use App\Models\SalesOrder;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ...$this->getStatusActions(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    private function getStatusActions(): array
    {
        /** @var SalesOrder $record */
        $record = $this->getRecord();
        $actions = [];

        if ($record->status === 'pending') {
            $actions[] = Action::make('mark_under_review')
                ->label('Enviar a revisión')
                ->color('warning')
                ->icon('heroicon-o-eye')
                ->action(fn () => $this->updateStatus('under_review'));
        }

        if (in_array($record->status, ['pending', 'under_review'])) {
            $actions[] = Action::make('mark_cancelled')
                ->label('Cancelar')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->action(fn () => $this->updateStatus('cancelled'));
        }

        if ($record->status === 'under_review') {
            $actions[] = Action::make('mark_invoicing')
                ->label('Enviar a facturación')
                ->color('info')
                ->icon('heroicon-o-document-currency-dollar')
                ->action(fn () => $this->updateStatus('invoicing'));
        }

        return $actions;
    }

    private function updateStatus(string $status): void
    {
        /** @var SalesOrder $record */
        $record = $this->getRecord();
        $record->update(['status' => $status]);

        $labels = [
            'pending' => 'Pendiente',
            'under_review' => 'En Revisión',
            'invoicing' => 'En Facturación',
            'invoiced' => 'Facturado',
            'cancelled' => 'Cancelado',
        ];

        Notification::make()
            ->title("Orden actualizada a {$labels[$status]}")
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
    }
}
