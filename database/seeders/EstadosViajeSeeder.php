<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosViajeSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['estado' => 'EN_CURSO'],
            ['estado' => 'FINALIZADO'],
            ['estado' => 'CANCELADO'],
            ['estado' => 'PAUSADO'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_viaje')->updateOrInsert(
                ['estado' => $estado['estado']],
                array_merge($estado, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
