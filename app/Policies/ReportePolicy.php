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
     *  Quien puede ver reportes
     */
    public function ShowReport(User $user, Reportes $reportes): bool
    {

    return $user->hasAnyPermission([
             'ver_reportes_dependencia',
             'ver_reportes_general',
             'ver_reporte_iniciado',
             'ver_reportes_operativos',
        ]);
        return false;
    }

    /**
     * Quien puede crear reportes
     */
    public function createReport(User $user): bool
    {
         return $user->hasAnyPermission([
            'iniciar_reporte_interno',
        ]);
        return false;

    }

    /**
     * Quien puede modificar reportes
     */
    public function update(User $user, Reportes $reportes): bool
    {
         return $user->hasAnyPermission([
               'actualizar_reportes'
        ]);
        return false;
    }

    /**
     * QUien puede dar de baja un reporte
     */
    public function delete(User $user, Reportes $reportes): bool
    {
        return false;
    }



}
