<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Vehiculo;
use App\Models\Dependencia;
use App\Models\EstadosVehiculo;
use App\Models\EstadosNafta;
use App\Models\Direcciones;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que existan estos registros en tu base de datos
        // Si no existen, crea primero los seeders para estas tablas

        $vehiculos = [
            [
                'dominio' => 'ABC123',
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'anio' => 2022,
                'kilometros' => 15000,
                'control_satelital' => true,
                'habilitado_prestamo' => true,
                'condiciones_prestamo' => 'Disponible para préstamos internos y externos',
                'VTV' => now()->addMonths(6),
                'id_dependencia_duena' => 1, // Ajusta según tu BD
                'id_direccion_actual' => 1,  // Ajusta según tu BD
                'id_estado_vehiculo' => 1,   // Disponible
                'id_estado_nafta' => 1,      // Lleno
            ],
            [
                'dominio' => 'DEF456',
                'marca' => 'Chevrolet',
                'modelo' => 'S10',
                'anio' => 2021,
                'kilometros' => 28000,
                'control_satelital' => true,
                'habilitado_prestamo' => true,
                'condiciones_prestamo' => 'Solo préstamos internos',
                'VTV' => now()->addMonths(3),
                'id_dependencia_duena' => 1,
                'id_direccion_actual' => 1,
                'id_estado_vehiculo' => 2, // En uso
                'id_estado_nafta' => 2,    // Medio
            ],
            [
                'dominio' => 'GHI789',
                'marca' => 'Ford',
                'modelo' => 'Ranger',
                'anio' => 2023,
                'kilometros' => 5000,
                'control_satelital' => true,
                'habilitado_prestamo' => true,
                'condiciones_prestamo' => 'Vehículo nuevo, disponible',
                'VTV' => now()->addYear(),
                'id_dependencia_duena' => 2,
                'id_direccion_actual' => 2,
                'id_estado_vehiculo' => 1, // Disponible
                'id_estado_nafta' => 1,    // Lleno
            ],
            [
                'dominio' => 'JKL012',
                'marca' => 'Volkswagen',
                'modelo' => 'Amarok',
                'anio' => 2020,
                'kilometros' => 45000,
                'control_satelital' => false,
                'habilitado_prestamo' => false,
                'condiciones_prestamo' => null,
                'VTV' => now()->addMonths(2),
                'id_dependencia_duena' => 1,
                'id_direccion_actual' => 1,
                'id_estado_vehiculo' => 3, // Mantenimiento
                'id_estado_nafta' => 3,    // Bajo
            ],
            [
                'dominio' => 'MNO345',
                'marca' => 'Nissan',
                'modelo' => 'Frontier',
                'anio' => 2022,
                'kilometros' => 18000,
                'control_satelital' => true,
                'habilitado_prestamo' => true,
                'condiciones_prestamo' => 'Disponible con autorización',
                'VTV' => now()->addMonths(8),
                'id_dependencia_duena' => 2,
                'id_direccion_actual' => 2,
                'id_estado_vehiculo' => 1, // Disponible
                'id_estado_nafta' => 2,    // Medio
            ],
            [
                'dominio' => 'PQR678',
                'marca' => 'Mitsubishi',
                'modelo' => 'L200',
                'anio' => 2019,
                'kilometros' => 62000,
                'control_satelital' => true,
                'habilitado_prestamo' => true,
                'condiciones_prestamo' => 'Solo para viajes cortos',
                'VTV' => now()->addMonth(),
                'id_dependencia_duena' => 3,
                'id_direccion_actual' => 3,
                'id_estado_vehiculo' => 2, // En uso
                'id_estado_nafta' => 4,    // Vacío
            ],
            [
                'dominio' => 'STU901',
                'marca' => 'Renault',
                'modelo' => 'Duster',
                'anio' => 2021,
                'kilometros' => 32000,
                'control_satelital' => false,
                'habilitado_prestamo' => false,
                'condiciones_prestamo' => null,
                'VTV' => now()->addMonths(5),
                'id_dependencia_duena' => 1,
                'id_direccion_actual' => 1,
                'id_estado_vehiculo' => 4, //en mantenimineto
                'id_estado_nafta' => 1,    // Lleno
            ],
            [
                'dominio' => 'VWX234',
                'marca' => 'Fiat',
                'modelo' => 'Toro',
                'anio' => 2023,
                'kilometros' => 8000,
                'control_satelital' => true,
                'habilitado_prestamo' => true,
                'condiciones_prestamo' => 'Disponible para cualquier uso',
                'VTV' => now()->addYear(),
                'id_dependencia_duena' => 2,
                'id_direccion_actual' => 2,
                'id_estado_vehiculo' => 1, // Disponible
                'id_estado_nafta' => 1,    // Lleno
            ],
        ];

        foreach ($vehiculos as $vehiculo) {
            Vehiculo::create($vehiculo);
        }
    }
}
