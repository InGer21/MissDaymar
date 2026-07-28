<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['profit_code']);
            $table->dropColumn(['profit_code', 'profit_line', 'profit_subl']);
        });

        Schema::table('product_presentations', function (Blueprint $table) {
            $table->dropColumn(['profit_unit_code', 'profit_equivalence']);
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->dropIndex(['profit_code']);
            $table->dropColumn(['profit_code', 'profit_vendor', 'profit_zone']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['profit_code']);
            $table->dropColumn(['profit_code', 'is_salesperson']);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropIndex(['profit_doc_num']);
            $table->dropColumn('profit_doc_num');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('profit_code', 30)->nullable();
            $table->string('profit_line', 6)->nullable();
            $table->string('profit_subl', 6)->nullable();
        });

        Schema::table('product_presentations', function (Blueprint $table) {
            $table->string('profit_unit_code', 6)->nullable();
            $table->decimal('profit_equivalence', 18, 5)->nullable();
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->string('profit_code', 16)->nullable();
            $table->string('profit_vendor', 6)->nullable();
            $table->string('profit_zone', 6)->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profit_code', 6)->nullable();
            $table->boolean('is_salesperson')->default(false);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('profit_doc_num', 20)->nullable();
        });
    }
};
