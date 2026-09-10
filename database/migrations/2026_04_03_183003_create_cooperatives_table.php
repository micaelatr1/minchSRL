<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cooperatives', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('concession', 250);
            $table->string('mine', 250);
            $table->string('municipality', 150);
            $table->string('NIM', 200);
            $table->string('NIT', 200);
            $table->decimal('contribution', 4, 2);
            $table->decimal('comibol', 4, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperatives');
    }
};
