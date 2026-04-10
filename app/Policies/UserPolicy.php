<?php

namespace App\Policies;

use App\Models\User;
/**
 * @brief UserPolicy marca las reglas  para gestionar usuarios en el sistema.
 * @description La UserPolicy gestiona el acceso de los diferentes roles en el sistema.
 * Admin general acceso a todo -- demas roles solo visualizacion y accion a nivel dependencia y area.
 */
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

    public function editarFotoPerfil(User $user, User $objetivo): bool
{
    // Puede editar SU PROPIA foto si tiene permiso
    if ($user->id === $objetivo->id) {
        return $user->can('editarFotoPerfil');
    }

    // Si querés que admin también pueda editar cualquiera:
    if ($user->hasRole('Administrador General')) {
        return true;
    }

    return false;
}

    /**
     * Ver listado general de usuarios
     * - Dueño Dependencia: solo personal de su dependencia
     */
    public function showUsers(User $user): bool
    {
        return $user->can('ver_personal_dependencia')
            || $user->can('ver_todos_usuarios');
    }

    /**
     * Ver un usuario puntual
     */
    public function showUser(User $user, User $objetivo): bool
    {
        // Dueño Dependencia → solo su dependencia
        if ($user->hasRole('Dueño Dependencia')) {
            return $objetivo->id_dependencia === $user->id_dependencia;
        }

        return false;
    }

    /**
     * Crear usuario
     */
    public function createUser(User $user, int $dependenciaId): bool
    {
        // Dueño Dependencia → solo puede crear en su dependencia
        if ($user->hasRole('Dueño Dependencia')) {
            return $user->id_dependencia === $dependenciaId
                && $user->can('crear_usuario');
        }

        if($user->hasRole('Administrador General')){
            return true;
        }

        return false;
    }

    /**
     * Actualizar usuario
     */
    public function update(User $user, User $objetivo): bool
    {
        if ($user->hasRole('Dueño Dependencia')) {
            return $objetivo->id_dependencia === $user->id_dependencia
                && $user->can('editar_personal_dependencia');
        }

        return false;
    }

    /**
     * Eliminar usuario
     */
    public function delete(User $user, User $objetivo): bool
    {
        if ($user->hasRole('Dueño Dependencia')) {
            return $objetivo->id_dependencia === $user->id_dependencia
                && $user->can('eliminar_personal_dependencia');
        }

        return false;
    }

    /**
     * Asignar rol a un usuario
     */
    public function assignRole(User $user, User $objetivo, string $rol): bool
    {
        // Dueño Dependencia NO puede escalar roles críticos
        if ($user->hasRole('Dueño Dependencia')) {
            return !in_array($rol, [
                'Administrador General',
            ]);
        }

        return false;
    }
}
