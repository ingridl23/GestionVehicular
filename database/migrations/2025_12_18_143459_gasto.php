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
        Schema::create('gasto', function(Blueprint $table){
            $table->id();
            $table->integer('kilometros'); //en metros
            $table->foreignId('id_estado_nafta')->references('id')->on('estados_naftas')->onDelete('cascade');
            $table->foreignId('id_viaje')->references('id')->on('viajes')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto');
    }
};
