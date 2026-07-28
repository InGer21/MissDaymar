<?php

namespace App\Filament\Resources\DispatchedOrders\Pages;

use App\Filament\Resources\DispatchedOrders\DispatchedOrderResource;
use App\Filament\Resources\SalesOrders\SalesOrderResource;
use App\Models\SalesOrder;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDispatchedOrder extends EditRecord
{
    protected static string $resource = DispatchedOrderResource::class;

    protected function getHeaderActions(): array
    {
        /** @var SalesOrder $record */
        $record = $this->getRecord();

        return [
            ViewAction::make(),
            // Deshacer un despacho marcado por error: el pedido regresa a
            // Pedidos Generados.
            Action::make('undo_dispatch')
                ->label('Deshacer Despacho')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Deshacer el despacho')
                ->modalDescription('El pedido volverá a Pedidos Generados.')
                ->action(function () use ($record) {
                    $record->update(['dispatched_at' => null]);

                    Notification::make()
                        ->title('Despacho deshecho')
                        ->success()
                        ->send();

                    $this->redirect(SalesOrderResource::getUrl('edit', ['record' => $record]));
                }),
        ];
    }
}
