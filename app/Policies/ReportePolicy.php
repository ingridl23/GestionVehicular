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

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Administrador General')) {
            return true;
        }
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
    public function createReport(User $user): bool
    {
        return $user->hasPermissionTo('iniciar_reporte_interno');
    }

    /**
     * Agregar mensajes / comentarios
     */
    public function createMessage(User $user): bool
    {
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
    {
        return $user->hasPermissionTo('actualizar_reportes');
    }

    /**
     * Eliminar reporte (por ahora nadie)
     */
    public function delete(User $user, Reportes $reporte): bool
    {
        return false;
    }





}
