<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ROLES
        $adminDependencia = Role::firstOrCreate([
            'name' => 'Administrador de Dependencia',
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
    'asignar_conductor_suplente',

    // Reportes
    'ver_reportes_dependencia',
    'ver_reportes_general',
    'ver_reporte_iniciado',
    'actualizar_viaje',
    'iniciar_reporte_interno',
    // Auditoría
    'ver_auditoria',
    'ver_gastos',
    'descargar_datos',

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
    'crear_dependencias',
    'editar_dependencias',
    'eliminar_dependencias',
    'ver_dependencias_hijas',
    'crear_dependencias_hijas',
    'editar_dependencias_hijas',
    'eliminar_dependencias_hijas',
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
    'ver_menu',
    // ==========================
    // NUEVOS PERMISOS
    // ==========================

    // Vehículos
    'ver_vehiculos_dentro_dependencia',

    // Reserva
    'asignar_conductor_en_reserva_activa',
    'ver_reservas_dependencia_en_curso',
    'actualizar_reserva_operativa',
    'finalizar_reserva_interna',
    'ver_historial_reservas',
    // Reportes
    'ver_reportes_operativos',
    'actualizar_reportes'
];


        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

// ASIGNACIÓNES A CADA ROL DENTRO DEL SISTEMA
$adminDependencia->syncPermissions([
    'cargar_vehiculo',
    'editar_vehiculo',
    'eliminar_vehiculo',
    'ver_vehiculos',
    'modificar_asignacion_vehiculo',
    'asignar_conductor_suplente',
    'cambiar_estado_vehiculo',
    'ver_reportes_dependencia',
    'ver_reservas_internas',
    'ver_reservas_prestamos',
    'autorizar_prestamos',
    'autorizar_reservas_internas',
    'ver_solicitudes_prestamos',
    'actualizar_prestamo',
    'cancelar_reserva_interna',
    'cancelar_prestamo',
    'solicitar_reserva_interna',
    'solicitar_prestamo',
    'ver_auditoria',
    'ver_gastos',
    'visualizar_reserva_asignada',
    'ver_personal_dependencia',
    'ver_menu',
    'editar_personal_dependencia',
    'eliminar_personal_dependencia',

    // nuevos:
    'asignar_conductor_en_reserva_activa',
    'ver_reservas_dependencia_en_curso',
    'ver_reportes_operativos',
    'actualizar_reportes',
     'ver_dependencias',
    'crear_dependencias_hijas',
    'editar_dependencias_hijas',
      // + permisos operativos:
    'ver_vehiculos_dentro_dependencia',
    'actualizar_reserva_interna',
    'iniciar_reporte_interno',
    'ver_reporte_iniciado',
    'actualizar_viaje',
    'ver_reservas_dependencia_en_curso',
    'actualizar_reserva_operativa',
    'asignar_conductor_en_reserva_activa',
    'ver_reportes_operativos',
    'finalizar_reserva_interna',
    'descargar_datos'

]);


 $jefeOficina->syncPermissions([
            'solicitar_reserva_interna',
            'solicitar_prestamo',
            'ver_reservas_internas',
            'ver_reservas_prestamos',
            'ver_reportes_dependencia',
            'modificar_asignacion_vehiculo',
            'asignar_conductor_suplente',
            'ver_vehiculos_dentro_dependencia',
            'ver_reservas_dependencia_en_curso',
            'ver_reportes_operativos',
            'descargar_datos',
            'ver_menu'
        ]);

$conductor->syncPermissions([
    'ver_vehiculos_dentro_dependencia',
    'ver_reservas_internas',
    'solicitar_reserva_interna',
    'actualizar_reserva_interna',
    'iniciar_reporte_interno',
    'ver_reporte_iniciado',
    'actualizar_viaje',
    'asignar_conductor_suplente',

    // nuevos:
    'ver_reservas_dependencia_en_curso',
    'actualizar_reserva_operativa',
    'asignar_conductor_en_reserva_activa',
    'ver_reportes_operativos',
    'finalizar_reserva_interna',
    'ver_historial_reservas'
]);


        $adminGeneral->syncPermissions(Permission::all());
    }
}
