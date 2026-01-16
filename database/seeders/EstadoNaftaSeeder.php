<?php

namespace Database\Seeders;

use App\Models\EstadosNafta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoNaftaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        EstadosNafta::insert([
            [
                'estado'=> 'UN CUARTO ',
            ],
            [
                 'estado'=> 'MEDIO TANQUE',
            ],
            [
                 'estado'=> 'TRES CUARTO',
            ],
            [
                'estado'=> 'TANQUE LLENO',
            ],
            ]);
    }
}
