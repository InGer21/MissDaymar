<?php

namespace App\Models;

use Database\Factories\EntityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    /** @use HasFactory<EntityFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profit_code',
        'profit_vendor',
        'profit_zone',
        'type',
        'name',
        'rif',
        'sunagro',
        'fiscal_state',
        'fiscal_city',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
}
