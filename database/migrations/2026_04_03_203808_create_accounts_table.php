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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('account_number', 20);
            $table->string('initials', 7)->nullable();
            $table->string('color', 15)->nullable();
            $table->enum('currency_type', ['USD', 'EUR', 'BOB']);
            $table->enum('department', ['none', 'Human Resources', 'Inventory'])->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
