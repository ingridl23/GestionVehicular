<?php
namespace App\Policies;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReservaPolicy
{

    /**
     * Admin General → TODO
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Administrador General')) {
            return true;
        }
    }

    /**
     * Ver una reserva
     */
    public function view(User $user, Reserva $reserva): bool
    {
        // Operativo: solo su reserva
        if ($user->hasRole('Operativo')) {
            return $reserva->user_id === $user->id;
        }

        // Dueño Dependencia: reservas de su dependencia
        if ($user->hasRole('Dueño Dependencia','Jefe De Area')) {
            return $reserva->dependencia_id === $user->dependencia_id;
        }

        return false;
    }

    /**
     * Autorizar reserva
     */
    public function autorizar(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('Dueño Dependencia')
            && $reserva->dependencia_id === $user->dependencia_id
            && $reserva->estado === 'PENDIENTE';
    }

    /**
     * Finalizar reserva antes de tiempo
     */
    public function finalizar(User $user, Reserva $reserva): bool
    {
        if (!$user->hasPermissionTo('finalizar_reserva_interna') || !$user->hasPermissionTo('cancelar_prestamo')) {
        return false;
    }

    // Operativo → solo su reserva
    if ($user->hasRole('Operativo')) {
        return $reserva->user_id === $user->id;
    }

    // Dueño Dependencia → reservas de su dependencia
    if ($user->hasRole('Dueño Dependencia')) {
        return $reserva->dependencia_id === $user->dependencia_id;
    }

    return false;
    }

    //SOLICITAR RESERVA EXTERNAS E INTERNAS
     /**
     * Solicitar reserva
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('solicitar_reserva_interna','solicitar_prestamo');
    }



    /**
     * Cambiar conductor en reserva activa
     */
    public function cambiarConductor(User $user, Reserva $reserva): bool
    {
        return (
            $user->hasPermissionTo('asignar_conductor_suplente')
            && $reserva->dependencia_id === $user->dependencia_id
            && $reserva->estado === 'ACTIVA'
        );
    }

    /**
     * Ver estado de una reserva
     */
    public function verEstado(User $user, Reserva $reserva): bool
    {
        return $this->view($user, $reserva);
    }

}


/** se puede hacer una version por permisos pero igual ambas maneras se complementan  */
