<?php

namespace App\Filament\Resources\ReceivableOrders\Pages;

use App\Filament\Resources\ReceivableOrders\ReceivableOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListReceivableOrders extends ListRecords
{
    protected static string $resource = ReceivableOrderResource::class;
}
