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
         Schema::create('coordenadas_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vehiculo')->constrained('vehiculo')->cascadeOnDelete();
            $table->foreignId('id_viaje')->constrained('viaje')->cascadeOnDelete();
            $table->decimal('latitud',10,7);
            $table->decimal('longitud',10,7);
            $table->float('precision')->nullable();
            $table->dateTime('fecha_hora');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coordenadas_vehiculo');
    }
};
