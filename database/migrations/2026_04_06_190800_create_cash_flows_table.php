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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->date('date');                    // When transaction happened
            $table->decimal('amount', 12, 2);        // Money amount
            $table->enum('type', ['income', 'expense']);  // Is it money in or out?
            $table->string('category')->nullable();  // Food, Salary, Transport, etc
            $table->text('description')->nullable(); // Extra notes
            $table->timestamps();                    // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
