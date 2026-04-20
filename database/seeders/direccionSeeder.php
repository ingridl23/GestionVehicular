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
                'calle' => 'Avenida Rivadavia', //1
                'altura' => '1',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Maipu',  //2
                'altura' => '274',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Ituzaingó',//3
                'altura' => '320',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'La Rioja',  //4
                'altura' => '99',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => '9 de julio',  //5
                'altura' => '56',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Entre 50 y 52, calle 29', //6
                'altura' => '0',
                'ciudad' => 'Reta',
            ],
            [
                'calle' => 'Girado', //7
                'altura' => '663',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => '9 de julio y España', //8
                'altura' => '0',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida San Martin y Pedro Aramburu',//9
                'altura' => '287',
                'ciudad' => 'Copetonas',
            ],
            [
                'calle' => 'Avenida San Martin',//10
                'altura' => '323',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Moreno',//11
                'altura' => '245',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Brandsen', //12
                'altura' => '181',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Sadi Carnot', //13
                'altura' => '446',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Pellegrini', //14
                'altura' => '17',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => '1810', //15
                'altura' => '475',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Ruta Nacion Nº3 KM', //16
                'altura' => '499',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Castelli',//17
                'altura' => '625',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Guemes',//18
                'altura' => '2000',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Leandro N. Alem',//19
                'altura' => '2500',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Castelli',//20
                'altura' => '745',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida San Martin y Avenida Almafuerte',//21
                'altura' => '0',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Domingo Vásquez',//22
                'altura' => '476',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Pedro N. Carrera',//23
                'altura' => '940',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Guemes',//24
                'altura' => '630',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Avenida Primera Junta',//25
                'altura' => '440',
                'ciudad' => 'Tres Arroyos',
            ],
            [
                'calle' => 'Dirección Salud',//26
                'altura' => '0',
                'ciudad' => 'Tres Arroyos',
            ],

             [
                'calle' => 'calle esquina', //27
                'altura' => '28',
                'ciudad' => 'Claromeco',
            ],

             [
                'calle' => 'ruta nacional n°3', //28
                'altura' => '496',
                'ciudad' => 'Tres Arroyos',
            ],

        ]);
    }
}
