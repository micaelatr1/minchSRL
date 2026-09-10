<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->text('description');
            $table->decimal('total_amount', 11, 2);
            $table->decimal('paid_amount', 11, 2)->default(0);
            $table->unsignedBigInteger('person_id');
            $table->enum('type', ['supplier', 'customer']);
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('file', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
