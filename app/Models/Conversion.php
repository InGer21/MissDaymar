<?php

namespace App\Models;

use Database\Factories\ConversionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversion extends Model
{
    /** @use HasFactory<ConversionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'notes',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ConversionItem::class);
    }
}
