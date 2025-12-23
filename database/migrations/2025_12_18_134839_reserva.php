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
        Schema::create('reserva', function(Blueprint $table){
            $table->id();
            $table->dateTime('fecha_reserva')->nullable(); // Es el día que registra la reserva, no la deberia cargar, lo toma directamente el sistema
            $table->dateTime('fecha_inicio_reserva')->nullable();
            $table->dateTime('fecha_fin_reserva')->nullable();
            $table->foreignId('id_vehiculo')->references('id')->on('vehiculos')->onDelete('cascade');
            $table->foreignId('id_estado_reserva')->references('id')->on('estados_reservas')->onDelete('restrict');
            $table->foreignId('id_dependencia_duena')->references('id')->on('dependencias')->onDelete('restrict');
            $table->foreignId('id_dependencia_solicitante')->references('id')->on('dependencias')->onDelete('restrict');
            $table->foreignId('id_usuario')->references('id')->on('users')->onDelete('restrict'); //Es quien realizará el viaje
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
