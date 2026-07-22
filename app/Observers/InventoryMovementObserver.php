<?php

namespace App\Observers;

use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryMovementObserver
{
    private function applyStockChange(InventoryMovement $movement, int $sign): void
    {
        $presentation = $movement->presentation;

        if (! $presentation) {
            return;
        }

        $delta = match ($movement->type) {
            'entry' => $movement->quantity,
            'exit' => -$movement->quantity,
            'adjustment' => $movement->quantity,
        };

        $newStock = $presentation->current_stock + ($delta * $sign);

        if ($newStock < 0) {
            throw new \RuntimeException(
                "Stock insuficiente: {$presentation->format} (actual: {$presentation->current_stock}, movimiento: ".($delta * $sign).')'
            );
        }

        $presentation->current_stock = $newStock;
        $presentation->saveQuietly();
    }

    public function created(InventoryMovement $movement): void
    {
        DB::transaction(fn () => $this->applyStockChange($movement, 1));
    }

    public function updated(InventoryMovement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $original = $movement->getOriginal();
            $presentation = $movement->presentation;

            if (! $presentation) {
                return;
            }

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

            $netChange = $newDelta - $oldDelta;
            $newStock = $presentation->current_stock + $netChange;

            if ($newStock < 0) {
                throw new \RuntimeException(
                    "Stock insuficiente: {$presentation->format}"
                );
            }

            $presentation->current_stock = $newStock;
            $presentation->saveQuietly();
        });
    }

    public function deleted(InventoryMovement $movement): void
    {
        DB::transaction(fn () => $this->applyStockChange($movement, -1));
    }
}
