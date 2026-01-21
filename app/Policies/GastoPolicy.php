<?php

namespace App\Policies;

use App\Models\Gasto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GastoPolicy
{
    /**
    'ver_auditoria',
    'ver_gastos',
    'descargar_datos',

     */
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
     * Determine whether the user can view the model.
     */
    public function viewAuditoria(User $user, Gasto $gasto): bool
    {
          return $user->hasAnyPermission([


             'ver_auditoria',
             'ver_gastos',
        ]);
        return false;
    }

    /**
     * Quien puede cargar un gasto (en relacion al viaje)
     */
    public function create(User $user): bool
    {
           return $user->hasAnyPermission([

         'registrar_datos_vehiculos',
           ]);
        return false;
    }

    /**
     *Quien puede modificar un gasto
     */
    public function update(User $user, Gasto $gasto): bool
    {
          return $user->hasAnyPermission([
         'registrar_datos_vehiculos',
         'ver_gastos'
           ]);
        return false;
    }

    /**
     * QUien puede dar de baja un gasto
     */
    public function delete(User $user, Gasto $gasto): bool
    {
        return false;
    }

    /**
     * QUien puede descargar estadistica o resumen de gastos del sistema.
     */
    public function restore(User $user, Gasto $gasto): bool
    {

          return $user->hasAnyPermission([

             'ver_auditoria',
             'ver_gastos',
        ]);

        return false;
    }


}
