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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit'); // Unit of measurement (e.g., g, kg, ml)
            $table->decimal('stock_quantity', 10, 2);
            $table->decimal('initial_stock', 10, 2);
            $table->timestamps();

            $table->index(['stock_quantity' , 'initial_stock']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
