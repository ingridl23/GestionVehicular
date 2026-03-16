<?php
namespace App\Policies;
use App\Models\Dependencia;
use App\Models\User;
/**
 * @brief Dependencia Policy marca las reglas  para gestionar dependencias y usuarios en el sistema.
 * @description La dependencia policy gestiona el acceso de los diferentes roles en el sistema.
 * Admin general acceso a todo -- demas roles solo visualizacion y accion a nivel dependencia y area.
 */
class DependenciaPolicy
{
    /**
     * Admin General → acceso total
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Administrador General')) {
            return true;
        }
    }

    public function vistaGeneral(User $user): bool{
        return $user->can('ver_dependencias');
    }


    /**
     * Ver dependencia
     */
    public function view(User $user, Dependencia $dependencia): bool
    {
        // Administrador de Dependencia → su dependencia
        if ($user->hasRole('Administrador de Dependencia')) {

                $idsPermitidos = array_merge(
                [$user->dependencia->id],
                $user->dependencia->obtenerIdsHijas()
            );
            return in_array($dependencia->id, $idsPermitidos);
        }

        // Jefe de Área → solo su dependencia
        if ($user->hasRole('Jefe de Area')) {
            return $user->id_dependencia === $dependencia->id;
        }

        if($user->hasRole('Adminsitrador General')){
            return true;
        }

        return false;
    }

    /**
     * Crear dependencia hija
     */
    public function create(User $user, ?Dependencia $dependenciaPadre = null): bool
    {
        if (! $user->can('crear_dependencias_hijas')) {
            return false;
        }

        // Solo puede crear hijas de su dependencia
        return $dependenciaPadre
            && $dependenciaPadre->id === $user->dependencia_id;
    }

    /**
     * Editar dependencia
     */
    public function update(User $user, Dependencia $dependencia): bool
    {
        if (! $user->can('editar_dependencias_hijas')) {
            return false;
        }

        return $dependencia->id === $user->dependencia_id
            || $dependencia->id_dependencia_padre === $user->dependencia_id;
    }

    /**
     * Eliminar dependencia
     */
    public function delete(User $user, Dependencia $dependencia): bool
    {
        if (! $user->can('eliminar_dependencias_hijas')) {
            return false;
        }

        // No puede eliminar su propia dependencia
        if ($dependencia->id === $user->dependencia_id) {
            return false;
        }

        return $dependencia->id_dependencia_padre === $user->dependencia_id;
    }

    /**
     * Activar / desactivar dependencia
     */
    public function toggle(User $user, Dependencia $dependencia): bool
    {
        return $user->can('editar_dependencias_hijas')
            && $dependencia->id_dependencia_padre === $user->dependencia_id;
    }
}

