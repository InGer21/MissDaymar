<?php

namespace App\Filament\Resources\DispatchedOrders\Pages;

use App\Filament\Resources\DispatchedOrders\DispatchedOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDispatchedOrder extends ViewRecord
{
    protected static string $resource = DispatchedOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
