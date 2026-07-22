<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->string('fiscal_state', 100)->nullable()->change();
            $table->string('fiscal_city', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->string('fiscal_state', 100)->nullable(false)->change();
            $table->string('fiscal_city', 100)->nullable(false)->change();
        });
    }
};
