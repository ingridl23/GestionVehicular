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
        Schema::create('dependencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activa')->default(true);

            $table->foreignId('id_direccion')
                ->constrained('direcciones')
                ->restrictOnDelete();

            $table->foreignId('id_dependencia_padre')
                ->nullable()
                ->constrained('dependencias')
                ->restrictOnDelete();

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
