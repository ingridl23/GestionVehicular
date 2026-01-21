<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
     * Quien puede ver listado del personal
     */
    public function showUsers(User $user, User $User): bool
    {

    //solo de su dependendencia
    if ($user->hasRole('Dueño Dependencia') ) {
        return $User->dependencia_id === $user->dependencia_id;

        }

        return false;
    }

    /**
     * Quien puede agregar personal a una dependencia
     */
    public function createUsers(User $user,  User  $User): bool
    {
         //solo de su dependendencia
    if ($user->hasRole('Dueño Dependencia') ) {
        return $User->dependencia_id === $user->dependencia_id;

        }
        return false;
    }

    /**
     * Quien puede actualizar personal de una dependencia
     */
    public function updateUsers(User $user, User  $User): bool
    {
         //solo de su dependendencia
    if ($user->hasRole('Dueño Dependencia') ) {
        return $User->dependencia_id === $user->dependencia_id;

        }
        return false;
    }

    /**
     * Quien puede eliminar personal de una dependencia
     */
    public function deleteUser(User $user, User $User): bool
    {
         //solo de su dependendencia
    if ($user->hasRole('Dueño Dependencia') ) {
        return $User->dependencia_id === $user->dependencia_id;

        }
        return false;
    }

}
