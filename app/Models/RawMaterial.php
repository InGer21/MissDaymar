<?php

namespace App\Models;

use Database\Factories\RawMaterialFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function stock(): Attribute
    {
        return Attribute::get(fn () => $this->product?->total_stock ?? 0);
    }

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:4',
        ];
    }
}
