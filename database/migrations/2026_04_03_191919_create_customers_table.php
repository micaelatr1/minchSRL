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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('file', 150)->nullable();
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('cooperative_id')->nullable();
            $table->foreign('cooperative_id')->references('id')->on('cooperatives')->onDelete('set null');
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
