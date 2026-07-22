<?php

namespace App\Observers;

use App\Models\PresentationPrice;
use App\Models\ProductPresentation;

class ProductPresentationObserver
{
    public function saved(ProductPresentation $presentation): void
    {
        $price = session()->pull('price_usd_'.$presentation->id);

        if ($price === null || $price === '') {
            return;
        }

        PresentationPrice::updateOrCreate(
            [
                'presentation_id' => $presentation->id,
                'line' => 1,
            ],
            [
                'price_usd' => (float) $price,
                'unit_price_usd' => null,
            ]
        );
    }
}
