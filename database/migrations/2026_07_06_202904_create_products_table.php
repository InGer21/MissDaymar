<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20); // raw_material, finished, both, pure
            $table->string('line_1', 50)->nullable();
            $table->string('line_2', 50)->nullable();
            $table->boolean('is_pure')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
