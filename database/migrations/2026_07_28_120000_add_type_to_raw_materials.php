<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // grano | consumible — separa los granos en saco de los consumibles
            // de empaque (bobinas, etc.). Default 'grano' porque hoy el 100%
            // de los registros existentes son granos.
            $table->string('type', 20)->default('grano')->after('code');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
