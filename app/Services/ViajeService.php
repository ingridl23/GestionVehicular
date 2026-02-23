<?php
namespace App\Services\Viajes;

use App\Models\Viaje;
use App\Models\Reserva;
use App\Models\EstadosReserva;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Gasto;
use App\Models\PrecioCombustible;
class ViajeService
{

    public function comenzarViaje(int $reservaId): Viaje
    {
        return DB::transaction(function () use ($reservaId) {

            $user = Auth::user();

            $reserva = Reserva::with('vehiculo')->findOrFail($reservaId);

            //  Validar conductor
            if ($reserva->id_usuario !== $user->id) {
                throw ValidationException::withMessages([
                    'usuario' => 'No puede iniciar un viaje que no le pertenece.'
                ]);
            }

            //  Validar estado reserva
            if ($reserva->id_estado_reserva !== EstadosReserva::APROBADA) {
                throw ValidationException::withMessages([
                    'reserva' => 'La reserva no está aprobada.'
                ]);
            }

            //  Verificar que no exista viaje activo
            $viajeActivo = Viaje::where('id_reserva', $reserva->id)
                ->whereNull('fecha_fin')
                ->first();

            if ($viajeActivo) {
                throw ValidationException::withMessages([
                    'viaje' => 'El viaje ya fue iniciado.'
                ]);
            }

            $vehiculo = $reserva->vehiculo;

            // Validar vehículo habilitado
            if (!$vehiculo->habilitado_prestamo) {
                throw ValidationException::withMessages([
                    'vehiculo' => 'El vehículo no está habilitado para préstamo.'
                ]);
            }

            // Validar estado vehículo (ej: 1 = disponible)
            if ($vehiculo->id_estado_vehiculo !== 1) {
                throw ValidationException::withMessages([
                    'vehiculo' => 'El vehículo no está disponible.'
                ]);
            }

            //  Crear viaje
            $viaje = Viaje::create([
                'id_reserva' => $reserva->id,
                'id_vehiculo' => $vehiculo->id,
                'fecha_inicio' => now(),
                'kilometros_inicio' => $vehiculo->kilometros,
                'id_estado_nafta_inicio' => $vehiculo->id_estado_nafta,
                'id_ultima_ubicacion' => $vehiculo->id_direccion_actual
            ]);

            //  Cambiar estado reserva
            $reserva->update([
                'id_estado_reserva' => EstadosReserva::EN_CURSO
            ]);

            return $viaje;
        });
    }

    public function finalizarViaje(int $viajeId, array $data): void
    {
        DB::transaction(function () use ($viajeId, $data) {

            $viaje = Viaje::with('vehiculo', 'reserva')->findOrFail($viajeId);

            if ($viaje->fecha_fin) {
                throw ValidationException::withMessages([
                    'viaje' => 'El viaje ya fue finalizado.'
                ]);
            }

            if ($data['kilometros_fin'] < $viaje->kilometros_inicio) {
                throw ValidationException::withMessages([
                    'kilometros_fin' => 'Los kilómetros finales no pueden ser menores a los iniciales.'
                ]);
            }

            $vehiculo = $viaje->vehiculo;

            $kmRecorridos = $data['kilometros_fin'] - $viaje->kilometros_inicio;

            // Calcular gasto automático
            $litrosConsumidos = $kmRecorridos * 0.10;

            $precio = PrecioCombustible::orderByDesc('fecha')->first();

            if (!$precio) {
                throw ValidationException::withMessages([
                    'precio' => 'No hay precio de combustible registrado.'
                ]);
            }

            $monto = $litrosConsumidos * $precio->precio_litro;

            // Actualizar viaje
            $viaje->update([
                'fecha_fin' => now(),
                'kilometros_fin' => $data['kilometros_fin'],
                'id_estado_nafta_fin' => $data['id_estado_nafta_fin'],
                'observaciones' => $data['observaciones'] ?? null
            ]);

            // Actualizar vehículo
            $vehiculo->update([
                'kilometros' => $data['kilometros_fin'],
                'id_estado_nafta' => $data['id_estado_nafta_fin'],
            ]);

            // Crear gasto
            Gasto::create([
                'id_viaje' => $viaje->id,
                'kilometros' => $kmRecorridos,
                'id_estados_nafta' => $data['id_estado_nafta_fin'],
                'monto' => $monto
            ]);

            // Finalizar reserva
            $viaje->reserva->update([
                'id_estado_reserva' => EstadosReserva::FINALIZADA
            ]);
        });
    }
}
