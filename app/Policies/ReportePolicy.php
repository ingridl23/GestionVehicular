<?php

namespace App\Policies;
use App\Models\Reportes;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportePolicy
{
    /**
     * Administrador General → acceso total
     */
    /**
      📝 Explicación
El método before() en Policies funciona así:

Si retorna true → ✅ Autorizado (no evalúa otros métodos)
Si retorna false → ❌ Denegado (no evalúa otros métodos)
Si retorna null → ⏭️ Continúa evaluando el método específico (createReport, etc.)

El código no tenía el return null, entonces para usuarios que no son Administrador General,
 el método before() retornaba implícitamente null en versiones antiguas de PHP,
 pero ahora puede estar retornando vacío o evaluándose incorrectamente.
     */

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Administrador General')) {
            return true;
        }
        //Retornar null para que continúe evaluando otros métodos (me olvide esto , que pava, con razon no andaba)
        return null;
    }

    /**
     * Ver reportes
     */
    public function showReport(User $user): bool
    {
        return $user->hasAnyPermission([
            'ver_reportes_dependencia',
            'ver_reportes_general',
            'ver_reporte_iniciado',
            'ver_reportes_operativos',
        ]);
    }

    /**
     * Crear reporte
     */
    public function createReport(?User $user): bool
    {
        // Si no hay usuario, denegar
        if (!$user) {
            return false;
        }

        // Admin general siempre puede
        if ($user->hasRole('Administrador General')) {
            return true;
        }

        // Operativos con el permiso pueden
        return $user->hasPermissionTo('iniciar_reporte_interno');
    }

    /**
     * Agregar mensajes / comentarios
     */
    public function createMessage(User $user, Reportes $reporte): bool
{

    // Admin general puede todo
    if ($user->hasRole('Administrador General')) {
        return true;
    }
    return $user->hasAnyPermission([
        'iniciar_reporte_interno',
        'ver_reportes_dependencia',
        'ver_reportes_general',
        'ver_reporte_iniciado',
        'ver_reportes_operativos',
    ]);
}

    /**
     * Actualizar estado del reporte
     */
    public function update(User $user, Reportes $reporte): bool
    { if ($user->hasRole('Administrador General') || ($user->hasRole('Administrador de Dependencia') ) ) {
        return true;
    }

    return $user->can('actualizar_reportes');
    }

    /**
     * Eliminar reporte (por ahora nadie)
     */
    public function delete(User $user, Reportes $reporte): bool
    {
        return false;
    }





}
