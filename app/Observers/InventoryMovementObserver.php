<?php

namespace App\Observers;

use App\Models\InventoryMovement;

class InventoryMovementObserver
{
    private function applyStockChange(InventoryMovement $movement, int $sign): void
    {
        $presentation = $movement->presentation;

        $delta = match ($movement->type) {
            'entry' => $movement->quantity,
            'exit' => -$movement->quantity,
            'adjustment' => $movement->quantity,
        };

        $presentation->current_stock += ($delta * $sign);
        $presentation->saveQuietly();
    }

    public function created(InventoryMovement $movement): void
    {
        $this->applyStockChange($movement, 1);
    }

    public function updated(InventoryMovement $movement): void
    {
        $original = $movement->getOriginal();
        $presentation = $movement->presentation;

        $oldDelta = match ($original['type']) {
            'entry' => $original['quantity'],
            'exit' => -$original['quantity'],
            'adjustment' => $original['quantity'],
        };
        $newDelta = match ($movement->type) {
            'entry' => $movement->quantity,
            'exit' => -$movement->quantity,
            'adjustment' => $movement->quantity,
        };

        $presentation->current_stock += ($newDelta - $oldDelta);
        $presentation->saveQuietly();
    }

    public function deleted(InventoryMovement $movement): void
    {
        $this->applyStockChange($movement, -1);
    }
}
