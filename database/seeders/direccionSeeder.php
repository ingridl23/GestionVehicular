<?php

namespace Database\Seeders;

use App\Models\Direcciones;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DireccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Direcciones::insert([
            [
                'calle' => 'Avenida Rivadavia',
                'altura' => '1',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Maipu',
                'altura' => '274',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Ituzaingó',
                'altura' => '320',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'La Rioja',
                'altura' => '99',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => '9 de julio',
                'altura' => '56',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Entre 50 y 52, calle 29',
                'altura' => '0',
                'ciudad' => 'Reta',
            ],
            [
                'calle' => 'Girado',
                'altura' => '663',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => '9 de julio y España',
                'altura' => '0',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida San Martin y Pedro Aramburu',
                'altura' => '287',
                'ciudad' => 'Copetonas',
            ],
            [
                'calle' => 'Avenida San Martin',
                'altura' => '323',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Moreno',
                'altura' => '245',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Brandsen',
                'altura' => '181',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Sadi Carnot',
                'altura' => '446',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Pellegrini',
                'altura' => '17',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => '1810',
                'altura' => '475',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Ruta Nacion Nº3 KM',
                'altura' => '499',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Castelli',
                'altura' => '625',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Guemes',
                'altura' => '2000',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Leandro N. Alem',
                'altura' => '2500',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Castelli',
                'altura' => '745',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida San Martin y Avenida Almafuerte',
                'altura' => '0',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Domingo Vásquez',
                'altura' => '476',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Pedro N. Carrera',
                'altura' => '940',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Guemes',
                'altura' => '630',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Primera Junta',
                'altura' => '440',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Dirección Salud',
                'altura' => '0',
                'ciudad' => 'Tres Arroyos',
            ],

             [
                'calle' => 'calle esquina',
                'altura' => '28 y 11',
                'ciudad' => 'Claromeco',
            ],

             [
                'calle' => 'ruta nacional n°3',
                'altura' => '496,5',
                'ciudad' => 'Tres Arroyos',
            ],

        ]);
    }
}
