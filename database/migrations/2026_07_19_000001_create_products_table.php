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
            $table->string('code', 20)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('category', ['materia_prima', 'insumo', 'repuesto', 'combustible', 'otro']);
            $table->enum('unit_of_measure', ['kg', 'ton', 'l', 'u', 'm']);
            $table->decimal('stock', 12, 2)->default(0);
            $table->decimal('average_cost', 12, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
