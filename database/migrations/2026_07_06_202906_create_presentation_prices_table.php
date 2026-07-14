<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presentation_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentation_id')->constrained('product_presentations')->cascadeOnDelete();
            $table->tinyInteger('line'); // 1 or 2
            $table->decimal('price_usd', 10, 2);
            $table->decimal('unit_price_usd', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presentation_prices');
    }
};
