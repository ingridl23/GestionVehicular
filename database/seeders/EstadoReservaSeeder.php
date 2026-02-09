<?php

namespace Database\Seeders;

use App\Models\EstadosReserva;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadosReserva::query()->create([
            'estado' => 'SOLICITADA',
        ]);

        EstadosReserva::query()->create([
            'estado' => 'APROBADA',
        ]);

        EstadosReserva::query()->create([
            'estado' => 'EN_CURSO',
        ]);

        EstadosReserva::query()->create([
            'estado' => 'FINALIZADA',
        ]);

        EstadosReserva::query()->create([
            'estado' => 'CANCELADA',
        ]);

        EstadosReserva::query()->create([
            'estado' => 'RECHAZADA',
        ]);
    }
}
