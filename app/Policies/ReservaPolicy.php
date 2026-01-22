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
     * Listado de reservas internas
     */

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver_reservas_internas');
    }

    /**
     * Listado de reservas externas
     */

    public function viewAnyLoan(User $user): bool
    {
        return $user->hasPermissionTo('ver_reservas_prestamos');
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
            return $reserva->id_dependencia_solicitante === $user->id_dependencia;
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
     * Cancelar reserva
     */
    public function cancelar(User $user): bool
    {
        if (!$user->hasPermissionTo('cancelar_reserva_interna') || !$user->hasPermissionTo('cancelar_prestamo')) {
            return false;
        }

        return true;
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
     *  (cualquier usuario puede ver un estado de reserva  que tenga estos permisos asignados)
     */
    public function verEstado(User $user, Reserva $reserva): bool
    {
          return $user->hasAnyPermission([

            'ver_reservas_internas',
            'ver_reservas_prestamos',
            'ver_solicitudes_prestamos',
            'autorizar_prestamos',
            'autorizar_reservas_internas',
            'actualizar_reserva_interna',
            'actualizar_prestamo',
            'visualizar_reserva_asignada',

        ]);

        return false;
    }

}



