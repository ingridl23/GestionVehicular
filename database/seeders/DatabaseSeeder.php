<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        $this->call([
            // 1️⃣ Catálogos base
            DireccionSeeder::class,
            DependenciaSeeder::class,

            // 2️⃣ Estados / catálogos del dominio
            EstadoVehiculoSeeder::class,
            EstadoNaftaSeeder::class,
            EstadoReservaSeeder::class,

            // 3️⃣ Seguridad
            RoleAndPermissionsSeeder::class,

            // 4️⃣ Usuarios (dependen de roles y dependencias)
            UserSeeder::class,

            // 5️⃣ Entidad principal
            VehiculoSeeder::class,
        ]);
    }
}
