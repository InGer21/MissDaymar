<?php

namespace App\Models;

use Database\Factories\InventoryMovementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    /** @use HasFactory<InventoryMovementFactory> */
    use HasFactory;

    protected $fillable = [
        'presentation_id',
        'type',
        'quantity',
        'referenceable_id',
        'referenceable_type',
        'notes',
    ];

    public function presentation(): BelongsTo
    {
        return $this->belongsTo(ProductPresentation::class, 'presentation_id');
    }

    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }
}
