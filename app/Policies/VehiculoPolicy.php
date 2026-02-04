<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Auth\Access\Response;

class VehiculoPolicy
{

    /**
     * Administrador General → acceso total
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Administrador General')) {
            return true;
        }
    }

    /**
     * Ver listado de vehículos
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'ver_vehiculos',
            'ver_vehiculos_dentro_dependencia'
        ]);
    }

    /**
     * Ver un vehículo puntual
     */
    public function view(User $user, Vehiculo $vehiculo): bool
    {
        // Puede ver todos
        if ($user->hasPermissionTo('ver_vehiculos')) {
            return true;
        }

        // Solo vehículos de su dependencia
        if ($user->hasPermissionTo('ver_vehiculos_dentro_dependencia')) {
            return $vehiculo->id_dependencia_duena === $user->dependencia_id;
        }

        return false;
    }

    /**
     * Crear vehículo
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('cargar_vehiculo');
    }

    /**
     * Editar vehículo
     */
    public function update(User $user, Vehiculo $vehiculo): bool
    {
        if (!$user->hasPermissionTo('editar_vehiculo')) {
            return false;
        }

        // Dueño de dependencia: solo su dependencia
        if ($user->hasRole('Dueño Dependencia')) {
          return $vehiculo->id_dependencia_duena === $user->dependencia_id;

        }

        return true;
    }

    /**
     * Eliminar vehículo
     */
    public function delete(User $user, Vehiculo $vehiculo): bool
    {
        if (!$user->hasPermissionTo('eliminar_vehiculo')) {
            return false;
        }

        if ($user->hasRole('Dueño Dependencia')) {
      return $vehiculo->id_dependencia_duena === $user->dependencia_id;

        }

        return true;
    }

    /**
     * Cambiar estado del vehículo (activo, mantenimiento, baja)
     */
    public function cambiarEstado(User $user, Vehiculo $vehiculo): bool
    {
        return (
            $user->hasPermissionTo('cambiar_estado_vehiculo')
            && $vehiculo->id_dependencia_duena === $user->dependencia_id
        );
    }

    /**
     * Modificar asignación de vehículo
     */
    public function modificarAsignacion(User $user, Vehiculo $vehiculo): bool
    {
        return (
            $user->hasPermissionTo('modificar_asignacion_vehiculo')
            && $vehiculo->id_dependencia_duena === $user->dependencia_id
        );
    }

    /**
     * Registrar datos del viaje (km, combustible, etc.)
     */
    public function registrarDatos(User $user, Vehiculo $vehiculo): bool
    {
        return (
            $user->hasPermissionTo('registrar_datos_vehiculos')
            && $vehiculo->id_dependencia_duena === $user->dependencia_id
        );
    }
}


