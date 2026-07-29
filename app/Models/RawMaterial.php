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

    /** Grano en saco: así llega la mercancía importada. */
    public const TYPE_GRAIN = 'grano';

    /** Bobinas y empaques usados para reenvasar el grano. */
    public const TYPE_CONSUMABLE = 'consumible';

    protected $fillable = [
        'sku',
        'code',
        'type',
        'name',
        'product_id',
        'purchase_presentation',
        'unit',
        'current_stock',
        'kg_per_unit',
        'unit_cost',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * KG totales de grano en existencia = sacos x KG por saco.
     * Devuelve null (y la tabla muestra "—") cuando falta el peso por saco,
     * en vez de mostrar un 0 que se leería como "no hay grano".
     */
    public function totalKg(): Attribute
    {
        return Attribute::get(fn () => $this->kg_per_unit === null
            ? null
            : (float) $this->current_stock * (float) $this->kg_per_unit);
    }

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:4',
            'current_stock' => 'decimal:2',
            'kg_per_unit' => 'decimal:3',
        ];
    }
}
