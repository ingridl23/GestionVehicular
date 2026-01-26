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
    public function vistaIndividual(User $user, Reserva $reserva): bool
    {
        // Operativo: solo su reserva
        if ($user->hasRole('Operativo')) {
            return $reserva->user_id === $user->id;
        }

        // Dueño Dependencia: reservas que involucren a su dependencia (puede ser solicitante o no)
        if ($user->hasRole(['Dueño Dependencia','Jefe De Area'])) {

            $dependenciaUsuario = $user->dependencia;

            
            if ($reserva->id_dependencia_solicitante === $dependenciaUsuario->id ||
                $reserva->id_dependencia_duena === $dependenciaUsuario->id) {
                    return true;
            }

            // hijas
            $idsHijas = $dependenciaUsuario->obtenerIdsHijas();

            return in_array($reserva->id_dependencia_solicitante, $idsHijas)
                || in_array($reserva->id_dependencia_duena, $idsHijas);
            }

        if ($user->hasRole(['Administrador General'])) {
            return true;
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
    public function cancelar(User $user, Reserva $reserva): bool{
        return $user->hasAnyPermission(['cancelar_reserva_interna','cancelar_prestamo']);
    }

    //SOLICITAR RESERVA EXTERNAS E INTERNAS
     /**
     * Solicitar reserva
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['solicitar_reserva_interna','solicitar_prestamo']);
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



