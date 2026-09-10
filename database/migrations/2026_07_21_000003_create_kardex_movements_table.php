<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->string('type'); // purchase, sale, adjustment_in, adjustment_out
            $table->nullableMorphs('reference'); // reference_type, reference_id -> apunta a PurchaseItem, SaleItem, etc.
            $table->decimal('quantity_in', 15, 4)->default(0);
            $table->decimal('quantity_out', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 4); // costo de ESTE movimiento
            $table->decimal('balance_quantity', 15, 4); // saldo de stock después del movimiento
            $table->decimal('balance_avg_cost', 15, 4); // costo promedio después del movimiento
            $table->decimal('balance_total_value', 15, 4); // balance_quantity * balance_avg_cost
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex_movements');
    }
};
