<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->text('descripcion');

            // quien reporta
            $table->foreignId('id_usuario')->nullable()
                ->constrained('users')
                ->onDelete('restrict');

            // entidad asociada
            $table->string('entidad_tipo'); // vehiculo | reserva | viaje
            $table->unsignedBigInteger('entidad_id');

            // estado del reporte
            $table->string('estado')->default('pendiente');
            // pendiente | en_revision | atendido | cerrado

            $table->timestamps();

            $table->index(['entidad_tipo', 'entidad_id']);
        });

        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
