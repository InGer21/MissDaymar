<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sales_order_id',
        'invoice_number',
        'bcv_rate',
        'subtotal_usd',
        'igtf_usd',
        'total_usd',
        'issued_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /** Una factura está cobrada cuando tiene fecha de cobro. */
    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
