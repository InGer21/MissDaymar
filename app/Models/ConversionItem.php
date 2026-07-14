<?php

namespace App\Models;

use Database\Factories\ConversionItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversionItem extends Model
{
    /** @use HasFactory<ConversionItemFactory> */
    use HasFactory;

    protected $fillable = [
        'conversion_id',
        'presentation_id',
        'type',
        'quantity',
    ];

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion::class);
    }

    public function presentation(): BelongsTo
    {
        return $this->belongsTo(ProductPresentation::class, 'presentation_id');
    }
}
