<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // customer, supplier
            $table->string('name', 255);
            $table->string('rif', 20)->unique();
            $table->string('sunagro', 50)->nullable();
            $table->string('fiscal_state', 100);
            $table->string('fiscal_city', 100);
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
