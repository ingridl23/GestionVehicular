<?php

namespace Database\Seeders;

use App\Models\EstadosVehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class estadosVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        EstadosVehiculo::query()->create([
            'estado' => 'DISPONIBLE',
        ]);

        EstadosVehiculo::query()->create([
            'estado' => 'EN_USO',
        ]);

        EstadosVehiculo::query()->create([
            'estado' => 'BAJA',
        ]);

        EstadosVehiculo::query()->create([
            'estado' => 'EN_MANTENIMIENTO',
        ]);

        EstadosVehiculo::query()->create([
            'estado' => 'NO_DISPONIBLE',
        ]);

    }
}
