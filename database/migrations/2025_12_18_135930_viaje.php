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
     Schema::create('viaje', function (Blueprint $table) {
    $table->id();

    $table->foreignId('id_reserva')
        ->constrained('reservas')
        ->onDelete('cascade');

    $table->foreignId('id_vehiculo')
        ->constrained('vehiculos')
        ->onDelete('cascade');

    $table->dateTime('fecha_inicio');
    $table->dateTime('fecha_fin')->nullable();

    $table->integer('kilometros_inicio');
    $table->integer('kilometros_fin')->nullable();

    $table->foreignId('id_estado_nafta_inicio')
        ->constrained('estados_naftas')
        ->onDelete('restrict');

    $table->foreignId('id_estado_nafta_fin')
        ->nullable()
        ->constrained('estados_naftas')
        ->onDelete('restrict');

    $table->foreignId('id_ultima_ubicacion')
        ->nullable()
        ->constrained('direcciones')
        ->onDelete('restrict');

    $table->string('observaciones')->nullable();

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
