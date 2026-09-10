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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description');
            $table->enum('type', ['D', 'C', 'B']);
            $table->decimal('amount', 9, 2);
            $table->string('number_vol', 20)->nullable();
            $table->unsignedBigInteger('person_id')->nullable()->default(null);
            $table->unsignedBigInteger('contract_id')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('person_id')->references('id')->on('people')->onDelete('set null');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
