<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Viaje;
use Illuminate\Auth\Access\Response;
/**
 * @brief VehiculoPolicy marca las reglas  para gestionar vehiculos en el sistema.
 * @description La Vehiculopolicy gestiona el estado y caractristicas de vehiculos en el sistema.
 * Admin general acceso a todo -- demas roles solo visualizacion y accion a nivel dependencia y area.
 */
class ViajePolicy
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
     * Ver listado de viajes
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([

        ]);
    }
}


