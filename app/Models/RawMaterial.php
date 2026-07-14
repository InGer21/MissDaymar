<?php

namespace App\Models;

use Database\Factories\RawMaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    /** @use HasFactory<RawMaterialFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'product_id',
        'purchase_presentation',
        'unit',
        'unit_cost',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
