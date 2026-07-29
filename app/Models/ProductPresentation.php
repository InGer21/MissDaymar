<?php

namespace App\Models;

use Database\Factories\ProductPresentationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPresentation extends Model
{
    /** @use HasFactory<ProductPresentationFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Agrupación de `presentation_type` en las tres secciones de Producto
     * Terminado del panel. Cada tipo cae en exactamente un grupo.
     *
     * Los sacos se listan aparte porque no se venden por pedido: el stock
     * vendible los excluye a propósito (ver Product::total_stock,
     * StatsOverview, StockAlerts y SalesOrderForm).
     */
    public const TYPES_SACK = ['saco'];

    public const TYPES_BUNDLE = ['bulto', 'medio_bulto'];

    public const TYPES_LOOSE = ['bolsa_individual', 'bolsa_4kg', 'ristra', 'por_kilo'];

    /** Etiquetas en español de cada `presentation_type`. */
    public const TYPE_LABELS = [
        'saco' => 'Saco',
        'bulto' => 'Bulto',
        'medio_bulto' => 'Medio Bulto',
        'bolsa_4kg' => 'Bolsa 4kg',
        'bolsa_individual' => 'Bolsa Individual',
        'ristra' => 'Ristra',
        'por_kilo' => 'Por Kilo',
    ];

    protected $fillable = [
        'sku',
        'product_id',
        'presentation_type',
        'format',
        'unit',
        'is_active',
        'is_main_unit',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(PresentationPrice::class, 'presentation_id');
    }

    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'presentation_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'presentation_id');
    }

    public function conversionItems(): HasMany
    {
        return $this->hasMany(ConversionItem::class, 'presentation_id');
    }
}
