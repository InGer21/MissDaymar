<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentation_id')->constrained('product_presentations')->cascadeOnDelete();
            $table->string('type', 20); // entry, exit, adjustment
            $table->decimal('quantity', 10, 2);
            $table->nullableMorphs('referenceable');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
