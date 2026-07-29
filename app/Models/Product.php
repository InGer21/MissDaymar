<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Categorías que no se muestran en el panel. El negocio de especias se
     * lleva aparte y sin sistema, así que se oculta — los datos NO se borran,
     * para poder volver a mostrarlos quitando el slug de aquí.
     */
    public const HIDDEN_CATEGORY_SLUGS = ['especias'];

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'type',
        'line_1',
        'line_2',
        'is_pure',
        'is_service',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function presentations(): HasMany
    {
        return $this->hasMany(ProductPresentation::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function getTotalStockAttribute(): float
    {
        return (float) $this->presentations()
            ->whereNotIn('presentation_type', ['saco'])
            ->sum('current_stock');
    }
}
