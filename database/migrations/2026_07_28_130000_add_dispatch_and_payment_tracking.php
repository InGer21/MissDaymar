<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Despacho y cobranza se modelan como marcas de tiempo independientes,
        // no como estados nuevos: la máquina de estados actual
        // (pending -> under_review -> invoicing -> invoiced) se queda intacta,
        // y el descuento de stock sigue ocurriendo al facturar (InvoiceObserver).
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dateTime('dispatched_at')->nullable()->after('status');
            $table->index('dispatched_at');
        });

        // La cobranza es propiedad de la factura (es contra ella que se cobra).
        // Se usa `paid_at` y no un booleano `is_paid` para dejar la puerta
        // abierta a pagos parciales más adelante sin renombrar la columna.
        Schema::table('invoices', function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable()->after('issued_at');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropIndex(['dispatched_at']);
            $table->dropColumn('dispatched_at');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['paid_at']);
            $table->dropColumn('paid_at');
        });
    }
};
