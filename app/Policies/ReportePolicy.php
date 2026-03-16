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
Explicación
El método before() en Policies funciona así:

Si retorna true →  Autorizado (no evalúa otros métodos)
Si retorna false →  Denegado (no evalúa otros métodos)
Si retorna null →  Continúa evaluando el método específico (createReport, etc.)

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
    public function view(User $user, Reportes $reporte)
    {

        if( $user->hasAnyPermission([
            'ver_reportes_dependencia',
            'ver_reportes_general',
            'ver_reporte_iniciado',
            'ver_reportes_operativos',
        ])){
            return true;
        }

           // O si es el dueño del reporte
             return $reporte->usuario_id === $user->id;
    }

    /**
     * Crear reporte
     */
    public function create(?User $user): bool
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
     * Actualizar estado del reporte
     */
    public function update(User $user, Reportes $reporte): bool
    { if ($user->hasRole('Administrador General') || ($user->hasRole('Administrador de Dependencia') ) ) {
        return true;
    }

    return $user->can('actualizar_reportes');
    }




}
