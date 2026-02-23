<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Se obtienen los roles (en caso de no existir se crean)
        $roleAdminDependencia = Role::firstOrCreate(['name' => 'Administrador de Dependencia']);
        $roleJefeOficina = Role::firstOrCreate(['name' => 'Jefe de Area']);
        $roleOperativo = Role::firstOrCreate(['name' => 'Operativo']);
        $roleAdminGeneral = Role::firstOrCreate(['name' => 'Administrador General']);
        $roleComputos = Role::firstOrCreate(['name'=> 'Computos']);
        //Se crean los usuarios
        $adminDependenciaUser = User::query()->create([
            'name' => 'admin',
            'lastname' => 'dependencia',
            'email' => 'adminDependencia@gmail.com',
            'password' => Hash::make('123456789'),
            'legajo' => 0001,
            'id_dependencia' => 1,
            'email_verified_at' => now()
        ]);

        $jefeDeOficina = User::query()->create([
            'name' => 'jefe',
            'lastname' => 'oficina',
            'email' => 'jefeDeOficina@gmail.com',
            'password' => Hash::make('123456789'),
            'legajo' => 0002,
            'id_dependencia' => 1,
            'email_verified_at' => now()
        ]);

        $conductor = User::query()->create([
            'name' => 'conductor',
            'lastname' => 'perez',
            'email' => 'conductor@gmail.com',
            'password' => Hash::make('123456789'),
            'legajo' => 0003,
            'id_dependencia' => 2,
            'email_verified_at' => now()
        ]);

        $administradorGeneral = User::query()->create([
            'name' => 'administrador',
            'lastname' => 'general',
            'email' => 'administradorGeneral@gmail.com',
            'password' => Hash::make('123456789'),
            'legajo' => 0004,
            'id_dependencia' => 2,
            'email_verified_at' => now()
        ]);
   $adminComputos = User::query()->create([
            'name' => 'Computos',
            'lastname' => 'TsAs',
            'email' => 'computos@tresarroyos.gov.ar',
            'password' => Hash::make('Computos2026'),
            'legajo' => 10,
            'id_dependencia' => 26,
            'email_verified_at' => now()
        ]);

        $adminDependenciaUser->assignRole($roleAdminDependencia);
        $jefeDeOficina->assignRole($roleJefeOficina);
        $conductor->assignRole($roleOperativo);
        $administradorGeneral->assignRole($roleAdminGeneral);
        $adminComputos->assignRole($roleComputos);



    }
}
