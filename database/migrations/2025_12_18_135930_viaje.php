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
        Schema::create('viaje', function(Blueprint $table){
            $table->id();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('observaciones')->nullable();
            $table->foreignId('id_ultima_ubicacion')->references('id')->on('direcciones')->onDelete('restrict');
            $table->foreignId('id_reserva')->references('id')->on('reservas')->onDelete('cascade');
            $table->foreignId('id_vehiculo')->references('id')->on('vehiculos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('viaje');
    }
};
