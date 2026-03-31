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
        Schema::create('reporte_comentario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_id')->constrained('reporte')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->restrictOnDelete();
            $table->text('comentario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_comentario');
    }
};
