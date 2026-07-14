<?php

namespace App\Filament\Resources\SalesOrders\Pages;

use App\Filament\Resources\SalesOrders\SalesOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;

    public function dehydrate(): void
    {
        $this->recalculateTotal();

        if (method_exists(parent::class, 'dehydrate')) {
            parent::dehydrate();
        }
    }

    protected function recalculateTotal(): void
    {
        $this->data['total_usd'] = round(
            collect($this->data['items'] ?? [])->sum(fn ($item) => (float) ($item['subtotal_usd'] ?? 0)),
            2
        );
    }
}
