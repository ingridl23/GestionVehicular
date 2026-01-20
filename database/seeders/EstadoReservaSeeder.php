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


        EstadosReserva::insert([
             [
                'estado' => 'APROBADA',
            ],
            [
                'estado' => 'EN CURSO',
            ],
            [
                'estado' => 'FINALIZADA',
            ],
            [
                'estado' => 'PENDIENTE',
            ],
            [
                'estado' => 'CANCELADA',
            ],
            [
                'estado' => 'RECHAZADA',
            ],
        ]);
    }
}
