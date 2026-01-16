<?php

namespace Database\Seeders;

use App\Models\EstadosReserva;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoNaftaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        EstadosReserva::insert([
            [
                'estado' => 'EN CURSO',
            ],
            [
                'estado' => 'TERMINADA',
            ],
            [
                'estado' => 'PENDIENTE',
            ],
        ]);
    }
}
