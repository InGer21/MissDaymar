<?php

namespace App\Filament\Resources\DispatchedOrders\Pages;

use App\Filament\Resources\DispatchedOrders\DispatchedOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListDispatchedOrders extends ListRecords
{
    protected static string $resource = DispatchedOrderResource::class;
}
