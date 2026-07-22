<?php

namespace App\Observers;

use App\Models\Conversion;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class ConversionObserver
{
    public function created(Conversion $conversion): void
    {
        DB::transaction(function () use ($conversion) {
            foreach ($conversion->items as $item) {
                $movementType = match ($item->type) {
                    'input' => 'exit',
                    'output', 'sobrante' => 'entry',
                    'merma' => null,
                };

                if ($movementType === null) {
                    continue;
                }

                InventoryMovement::create([
                    'presentation_id' => $item->presentation_id,
                    'type' => $movementType,
                    'quantity' => $item->quantity,
                    'referenceable_type' => Conversion::class,
                    'referenceable_id' => $conversion->id,
                    'notes' => match ($item->type) {
                        'input' => "Insumo usado en Conversión #{$conversion->id}",
                        'output' => "Producto generado en Conversión #{$conversion->id}",
                        'sobrante' => "Sobrante devuelto de Conversión #{$conversion->id}",
                        default => null,
                    },
                ]);
            }
        });
    }
}
