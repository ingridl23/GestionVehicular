<?php
namespace App\Policies;
use App\Models\Dependencia;
use App\Models\User;

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

    /**
     * Ver dependencia
     */
    public function view(User $user, Dependencia $dependencia): bool
    {
        // Dueño de Dependencia → su dependencia
        if ($user->hasRole('Dueño Dependencia')) {
            return $user->dependencia_id === $dependencia->id
                || $dependencia->id_dependencia_padre === $user->dependencia_id;
        }

        // Jefe de Área → solo su dependencia
        if ($user->hasRole('Jefe de Area')) {
            return $user->dependencia_id === $dependencia->id;
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



/**
 * EJEMPLO PARA AGREGAR AL DEPENDENCIACONTROLLER :

$this->authorize('view', $dependencia);
$this->authorize('create', [Dependencia::class, $dependenciaPadre]);
$this->authorize('update', $dependencia);


*/
