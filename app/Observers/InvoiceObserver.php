<?php

namespace App\Observers;

use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\SalesOrder;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        $order = $invoice->salesOrder;

        if ($order->status === 'invoiced') {
            return;
        }

        foreach ($order->items as $item) {
            InventoryMovement::create([
                'presentation_id' => $item->presentation_id,
                'type' => 'exit',
                'quantity' => $item->quantity,
                'referenceable_type' => SalesOrder::class,
                'referenceable_id' => $order->id,
                'notes' => "Salida por facturación de Orden #{$order->id}",
            ]);
        }

        $order->status = 'invoiced';
        $order->save();
    }
}
