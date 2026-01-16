<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ROLES
        $adminDependencia = Role::firstOrCreate([
            'name' => 'Dueño Dependencia',
            'guard_name' => 'web',
        ]);

        $jefeOficina = Role::firstOrCreate([
            'name' => 'Jefe de Area',
            'guard_name' => 'web',
        ]);

        $conductor = Role::firstOrCreate([
            'name' => 'Operativo',
            'guard_name' => 'web',
        ]);

        $adminGeneral = Role::firstOrCreate([
            'name' => 'Administrador General',
            'guard_name' => 'web',
        ]);

        // PERMISOS
        $permisos = [
            // Vehículos
            'cargar_vehiculo',
            'editar_vehiculo',
            'eliminar_vehiculo',
            'ver_vehiculos',
            'cambiar_estado_vehiculo',
            'registrar_datos_vehiculos',
            'modificar_asignacion_vehiculo',

            // Reportes
            'ver_reportes_dependencia',
            'ver_reportes_general',

            // Auditoría
            'ver_auditoria',
            'ver_gastos',

            // Reservas
            'ver_reservas_internas',
            'ver_reservas_prestamos',
            'ver_solicitudes_prestamos',
            'autorizar_prestamos',
            'autorizar_reservas_internas',
            'actualizar_reserva_interna',
            'actualizar_prestamo',
            'cancelar_reserva_interna',
            'cancelar_prestamo',
            'solicitar_reserva_interna',
            'solicitar_prestamo',
            'visualizar_reserva_asignada',

            // Dependencias
            'ver_dependencias',
            'crear_dependencia',
            'editar_dependencia',
            'eliminar_dependencia',

            // Usuarios
            'crear_usuario',
            'editar_usuario',
            'eliminar_usuario',
            'ver_usuario',
            'ver_todos_usuarios',
            'asignar_rol',
            'ver_personal_dependencia',
            'editar_personal_dependencia',
            'eliminar_personal_dependencia',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

        // ASIGNACIÓN
        $adminDependencia->syncPermissions([
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
            'eliminar_personal_dependencia',
        ]);

        $jefeOficina->syncPermissions([
            'solicitar_reserva_interna',
            'solicitar_prestamo',
            'ver_reservas_internas',
            'ver_reservas_prestamos',
            'ver_reportes_dependencia',
            'visualizar_reserva_asignada',
            'modificar_asignacion_vehiculo',
        ]);

        $conductor->syncPermissions([
            'visualizar_reserva_asignada',
            'registrar_datos_vehiculos',
            'modificar_asignacion_vehiculo',
        ]);

        $adminGeneral->syncPermissions(Permission::all());
    }
}
