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
        Schema::create('vehiculo', function(Blueprint $table){
            $table->id();
            $table->string('dominio');
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio');
            $table->foreignId('id_direccion_actual')->references('id')->on('direcciones')->onDelete('restrict');
            $table->foreignId('id_estado_vehiculo')->references('id')->on('estados_vehiculo')->onDelete('restrict');
            $table->foreignId('id_dependencia_duena')->references('id')->on('dependencias')->onDelete('restrict');
            $table->foreignId('id_estado_nafta')->references('id')->on('estados_nafta')->onDelete('restrict');
            $table->boolean('prestamo')->default(false);
            $table->string('condiciones_prestamo')->nullable();
            $table->integer('kilometros');
            $table->integer('VTV');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculo');
    }
};
