<?php
use App\Services\AlertaService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('vehiculos', function(Blueprint $table){
            $table->id();
            $table->string('dominio')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio');
            $table->foreignId('id_direccion_actual')->references('id')->on('direcciones')->onDelete('restrict');
            $table->foreignId('id_estado_vehiculo')->references('id')->on('estados_vehiculos')->onDelete('restrict');
            $table->foreignId('id_dependencia_duena')->references('id')->on('dependencias')->onDelete('restrict');
            $table->foreignId('id_estado_nafta')->references('id')->on('estados_naftas')->onDelete('restrict');
            $table->boolean('control_satelital')->default(true); //por defecto al cargar primera vez
            //id de viculacion con api gps gestya
            $table->string('gestya_device_id')->nullable()->unique();
            $table->boolean('habilitado_prestamo')->default(true); //por defecto al cargar primera vez
            $table->string('condiciones_prestamo')->nullable();
            $table->integer('kilometros')->default(0);
            $table->date('vtv');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
