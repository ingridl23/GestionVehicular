<?php

namespace App\Policies;
use App\Models\Gasto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GastoPolicy
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
     * Ver listado de gastos
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'ver_auditoria',
            'ver_gastos',
        ]);
    }

    /**
     * Ver gasto puntual
     */
    public function view(User $user, Gasto $gasto): bool
    {
        return $user->hasAnyPermission([
            'ver_auditoria',
            'ver_gastos',
        ]);
    }

    /**
     * Generar gasto (desde viaje)
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('registrar_datos_vehiculos');
    }

    /**
     * Modificar gasto
     */
    public function update(User $user, Gasto $gasto): bool
    {
        return $user->hasAnyPermission([
            'registrar_datos_vehiculos',
            'ver_gastos',
        ]);
    }

    /**
     * Eliminar gasto
     */
    public function delete(User $user, Gasto $gasto): bool
    {
        return false;
    }

    /**
     * Ver resumen / estadísticas
     */
    public function viewResumen(User $user): bool
    {
        return $user->hasAnyPermission([
            'ver_auditoria',
            'ver_gastos',
        ]);
    }
}
