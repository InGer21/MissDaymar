<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('profit_code', 30)->nullable()->after('id')->index();
            $table->string('profit_line', 6)->nullable()->after('line_2');
            $table->string('profit_subl', 6)->nullable()->after('profit_line');
            $table->boolean('is_service')->default(false)->after('is_pure');
        });

        Schema::table('product_presentations', function (Blueprint $table) {
            $table->string('profit_unit_code', 6)->nullable()->after('presentation_type');
            $table->decimal('profit_equivalence', 18, 5)->nullable()->after('profit_unit_code');
            $table->boolean('is_main_unit')->default(false)->after('is_active');
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->string('profit_code', 16)->nullable()->after('id')->index();
            $table->string('profit_vendor', 6)->nullable()->after('profit_code');
            $table->string('profit_zone', 6)->nullable()->after('profit_vendor');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profit_code', 6)->nullable()->after('role')->index();
            $table->boolean('is_salesperson')->default(false)->after('profit_code');
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('profit_doc_num', 20)->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['profit_code', 'profit_line', 'profit_subl', 'is_service']);
        });

        Schema::table('product_presentations', function (Blueprint $table) {
            $table->dropColumn(['profit_unit_code', 'profit_equivalence', 'is_main_unit']);
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->dropColumn(['profit_code', 'profit_vendor', 'profit_zone']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profit_code', 'is_salesperson']);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('profit_doc_num');
        });
    }
};
