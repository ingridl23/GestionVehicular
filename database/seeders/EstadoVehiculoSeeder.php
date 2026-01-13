<?php

namespace Database\Seeders;

use App\Models\EstadosVehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        EstadosVehiculo::insert([
            [
                'estado' => 'DISPONIBLE',
            ],
            [
                'estado' => 'BAJA',
            ],
            [
                'estado' => 'EN MANTENIMIENTO',
            ],
            [
                'estado' => 'EN USO',
            ],
        ]);
    }
}
