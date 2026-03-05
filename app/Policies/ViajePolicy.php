<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Viaje;
use Illuminate\Auth\Access\Response;

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


