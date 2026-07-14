<?php

namespace App\Providers;

use App\Models\Conversion;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Observers\ConversionObserver;
use App\Observers\InventoryMovementObserver;
use App\Observers\InvoiceObserver;
use App\Observers\SalesOrderItemObserver;
use App\Observers\SalesOrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        InventoryMovement::observe(InventoryMovementObserver::class);
        SalesOrderItem::observe(SalesOrderItemObserver::class);
        SalesOrder::observe(SalesOrderObserver::class);
        Conversion::observe(ConversionObserver::class);
        Invoice::observe(InvoiceObserver::class);
    }
}
