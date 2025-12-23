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
        Schema::create('dependencia', function(Blueprint $table){
            $table->id();
            $table->string('nombre');
            $table->boolean('activa')->default(true);
            $table->foreignId('id_direccion')->references('id')->on('direcciones')->onDelete('cascade');
            $table->foreignId('id_dependencia_padre')->nullable()->references('id')->on('dependencias')->onDelete('restrict');
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
