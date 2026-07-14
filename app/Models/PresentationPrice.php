<?php

namespace App\Models;

use Database\Factories\PresentationPriceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresentationPrice extends Model
{
    /** @use HasFactory<PresentationPriceFactory> */
    use HasFactory;

    protected $fillable = [
        'presentation_id',
        'line',
        'price_usd',
        'unit_price_usd',
    ];

    public function presentation(): BelongsTo
    {
        return $this->belongsTo(ProductPresentation::class, 'presentation_id');
    }
}
