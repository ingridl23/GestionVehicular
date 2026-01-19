<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadosReserva;

class estadosReservaSeeder extends Seeder
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
