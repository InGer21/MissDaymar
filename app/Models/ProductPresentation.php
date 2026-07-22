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

    protected $fillable = [
        'product_id',
        'presentation_type',
        'profit_unit_code',
        'profit_equivalence',
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
