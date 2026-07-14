<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('presentation_type', 20); // bolsa_individual, por_kilo, ristra, bulto, medio_bulto, saco, bolsa_4kg
            $table->string('format', 50);             // "18 gr", "1 kg", "10x18 gr"
            $table->string('unit', 10);               // g, kg, unit, sack, multipack
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_presentations');
    }
};
