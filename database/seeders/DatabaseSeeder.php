<?php

namespace Database\Seeders;

use App\Models\EstadosReserva;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        $this->call([
            // Catálogos base
            DireccionSeeder::class,
            DependenciaSeeder::class,

            // Estados / catálogos del dominio
            EstadoVehiculoSeeder::class,
            EstadoNaftaSeeder::class,
            EstadoReservaSeeder::class,

            // Seguridad
            RoleAndPermissionsSeeder::class,

            // Usuarios (dependen de roles y dependencias)
            UserSeeder::class,

            // Entidad principal
            VehiculoSeeder::class,
            // estados de viajes
             EstadosViajeSeeder::class,
        ]);
    }
}
