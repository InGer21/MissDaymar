<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->decimal('bcv_rate', 12, 4);
            $table->decimal('subtotal_usd', 12, 2);
            $table->decimal('igtf_usd', 12, 2)->default(0);
            $table->decimal('total_usd', 12, 2);
            $table->dateTime('issued_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
