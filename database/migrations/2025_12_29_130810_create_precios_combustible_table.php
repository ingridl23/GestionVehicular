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
        Schema::create('precios_combustible', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio_litro', 10, 2);
            $table->date('fecha');
            $table->timestamps();

            $table->unique('fecha');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios_combustible');
    }
};
