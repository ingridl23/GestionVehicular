<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
   Schema::create('alerta', function (Blueprint $table) {
    $table->id();

    $table->string('tipo');
    $table->string('entidad_tipo'); // vehiculo | usuario | reserva
    $table->unsignedBigInteger('entidad_id');

    $table->string('mensaje');
    $table->string('nivel')->default('warning'); // info | warning | critica

    $table->boolean('activa')->default(true);
    $table->timestamp('fecha_generada');
    $table->timestamp('fecha_resuelta')->nullable();

    $table->timestamps();

    $table->index(['entidad_tipo', 'entidad_id']);
});

}

    public function down(): void
    {
Schema::dropIfExists('alerta');
    }
};
