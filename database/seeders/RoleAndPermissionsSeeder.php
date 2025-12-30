<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

         // --- Roles ---
        $adminDependencia = Role::firstOrCreate(['name' => 'Administrador de Dependencia']);
        $jefeOficina = Role::firstOrCreate(['name' => 'Jefe de Oficina']);
        $conductor = Role::firstOrCreate(['name' => 'Conductor']);
        $adminGeneral = Role::firstOrCreate(['name' => 'Administrador General']);


        // --- Permisos ---

        //Vehiculos
        Permission::create(['name' => 'cargar_vehiculo']);
        Permission::create(['name' => 'editar_vehiculo']);
        Permission::create(['name' => 'eliminar_vehiculo']);
        Permission::create(['name' => 'ver_vehiculos']);
        Permission::create(['name' => 'cambiar_estado_vehiculo']);
        Permission::create(['name' => 'registrar_datos_vehiculos']); //cargar kms, ultima ubicacion
        Permission::create(['name' => 'modificar_asignacion_vehiculo']);

        //Reportes
        Permission::create(['name' => 'ver_reportes_dependencia']);
        Permission::create(['name' => 'ver_reportes_general']);

        //Auditoria
        Permission::create(['name' => 'ver_auditoria']);
        Permission::create(['name' => 'ver_gastos']);

        //Reservas
        Permission::create(['name' => 'ver_reservas_internas']);
        Permission::create(['name' => 'ver_reservas_prestamos']); //Muestra aprobadas, rechazadas y canceladas
        Permission::create(['name' => 'ver_solicitudes_prestamos']); // Muestra las solicitudes pendientes
        Permission::create(['name' => 'autorizar_prestamos']);
        Permission::create(['name' => 'autorizar_reservas_internas']);
        Permission::create(['name' => 'actualizar_reserva_interna']);
        Permission::create(['name' => 'actualizar_prestamo']);
        Permission::create(['name' => 'cancelar_reserva_interna']);
        Permission::create(['name' => 'cancelar_prestamo']);
        Permission::create(['name' => 'solicitar_reserva_interna']);
        Permission::create(['name' => 'solicitar_prestamo']);
        Permission::create(['name' => 'visualizar_reserva_asignada']);

        //dependencias
        Permission::create(['name' => 'ver_dependencias']);
        Permission::create(['name' => 'crear_dependencia']);
        Permission::create(['name' => 'editar_dependencia']);
        permission::create(['name' => 'eliminar_dependencia']);

        //usuarios
        Permission::create(['name' => 'crear_usuario']);
        Permission::create(['name' => 'editar_usuario']);
        Permission::create(['name' => 'eliminar_usuario']);
        Permission::create(['name' => 'ver_usuario']);
        Permission::create(['name' => 'ver_todos_usuarios']);
        Permission::create(['name' => 'asignar_rol']);
        Permission::create(['name' => 'ver_personal_dependencia']);
        Permission::create(['name' => 'editar_personal_dependencia']);
        Permission::create(['name' => 'eliminar_personal_dependencia']);
        // En caso de tener que agregar una nueva persona en su dependencia necesitará solicitarlo al Administrador General


        // Asignación de permisos

        $adminDependencia->givePermissionTo([
            'cargar_vehiculo',
            'editar_vehiculo',
            'eliminar_vehiculo',
            'ver_vehiculos',
            'modificar_asignacion_vehiculo',
            'cambiar_estado_vehiculo',
            'ver_reportes_dependencia',
            'ver_reservas_internas',
            'ver_reservas_prestamos',
            'autorizar_prestamos',
            'autorizar_reservas_internas',
            'ver_solicitudes_prestamos',
            'actualizar_reserva_interna',
            'actualizar_prestamo',
            'cancelar_reserva_interna',
            'cancelar_prestamo',
            'solicitar_reserva_interna',
            'solicitar_prestamo',
            'ver_auditoria',
            'ver_gastos',
            'visualizar_reserva_asignada',
            'ver_personal_dependencia',
            'editar_personal_dependencia',
            'eliminar_personal_dependencia'
        ]);


        $jefeOficina->givePermissionTo([
            'solicitar_reserva_interna',
            'solicitar_prestamo',
            'ver_reservas_internas',
            'ver_reservas_prestamos',
            'ver_reportes_dependencia',
            'visualizar_reserva_asignada',
            'modificar_asignacion_vehiculo',
        ]);


        $conductor->givePermissionTo([
            'visualizar_reserva_asignada',
            'registrar_datos_vehiculos',
            'modificar_asignacion_vehiculo',
        ]);


        $adminGeneral->givePermissionTo(Permission::all());
    }
}

