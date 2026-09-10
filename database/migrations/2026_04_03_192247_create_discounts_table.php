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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('taxe_id')->nullable();
            $table->unsignedBigInteger('retention_id')->nullable();
            $table->timestamps();
            $table->foreign('taxe_id')->references('id')->on('taxes')->onDelete('set null');
            $table->foreign('retention_id')->references('id')->on('retentions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
