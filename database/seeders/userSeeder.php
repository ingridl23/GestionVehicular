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
       // $roleAdminDependencia = Role::firstOrCreate(['name' => 'Administrador de Dependencia']);
        //$roleJefeOficina = Role::firstOrCreate(['name' => 'Jefe de Area']);
       // $roleOperativo = Role::firstOrCreate(['name' => 'Operativo']);
        $roleAdminGeneral = Role::firstOrCreate(['name' => 'Administrador General']);

        //Se crean los usuarios


   $adminComputos = User::query()->create([
            'name' => 'Computos',
            'lastname' => 'TsAs',
            'email' => 'gestionvehicular@tresarroyos.gov.ar',
            'password' => Hash::make('Computos2026'),
            'legajo' => 10,
            'id_dependencia' => 26,
            'email_verified_at' => now()
        ]);

      //  $adminDependenciaUser->assignRole($roleAdminDependencia);
      //  $jefeDeOficina->assignRole($roleJefeOficina);
        //$conductor->assignRole($roleOperativo);
       // $administradorGeneral->assignRole($roleAdminGeneral);
        $adminComputos->assignRole($roleAdminGeneral);



    }
}
