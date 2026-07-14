<?php

namespace App\Observers;

use App\Models\SalesOrderItem;

class SalesOrderItemObserver
{
    public function saving(SalesOrderItem $item): void
    {
        if ($item->presentation_id && ! $item->product_id) {
            $item->product_id = $item->presentation->product_id;
        }

        if ($item->quantity && $item->unit_price_usd) {
            $item->subtotal_usd = round($item->quantity * $item->unit_price_usd, 2);
        }
    }

    public function saved(SalesOrderItem $item): void
    {
        static::recalculateOrderTotal($item);
    }

    public function deleted(SalesOrderItem $item): void
    {
        static::recalculateOrderTotal($item);
    }

    private static function recalculateOrderTotal(SalesOrderItem $item): void
    {
        $order = $item->salesOrder;
        $order->total_usd = $order->items()->sum('subtotal_usd');
        $order->saveQuietly();
    }
}
