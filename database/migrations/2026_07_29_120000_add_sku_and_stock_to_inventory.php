<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // SKU completo de 6 caracteres del esquema (ej. G00311).
            $table->string('sku', 10)->nullable()->unique()->after('id');

            // Existencia: sacos si es grano, bobinas si es consumible.
            $table->decimal('current_stock', 12, 2)->default(0)->after('unit');

            // KG de grano por saco. Columna aparte porque `purchase_presentation`
            // es texto libre inconsistente ("25 kg", "Saco 25kg", "24 x 500",
            // vacío en buena parte de las filas) y no se puede derivar de ahí
            // de forma confiable.
            $table->decimal('kg_per_unit', 10, 3)->nullable()->after('current_stock');
        });

        Schema::table('product_presentations', function (Blueprint $table) {
            // SKU completo de 6 caracteres. Hasta ahora solo se guardaba el
            // prefijo de 4 en `products.sku`, así que todas las presentaciones
            // de un producto compartían código.
            $table->string('sku', 10)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn(['sku', 'current_stock', 'kg_per_unit']);
        });

        Schema::table('product_presentations', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });
    }
};
