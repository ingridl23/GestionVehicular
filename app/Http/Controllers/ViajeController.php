<?php
namespace App\Http\Controllers;
use App\Models\Viaje;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;



class ViajeController extends Controller{

/*
 protected ViajeService $service;

    public function __construct(ViajeService $service)
    {
        $this->service = $service;
    }
*/

public function comenzarViaje($reservaId)
{
    $reserva = Reserva::with('vehiculo')->findOrFail($reservaId);

    //  Validar que la reserva esté aprobada
    if ($reserva->estado !== 'aprobada') {
        return back()->withErrors('La reserva no está aprobada.');
    }

    //  Verificar que no exista ya un viaje activo
    $viajeExistente = Viaje::where('id_reserva', $reserva->id)
        ->whereNull('fecha_fin')
        ->first();

    if ($viajeExistente) {
        return back()->withErrors('El viaje ya fue iniciado.');
    }

    $vehiculo = $reserva->vehiculo;

    //  Crear el viaje
    $viaje = Viaje::create([
        'id_reserva' => $reserva->id,
        'id_vehiculo' => $vehiculo->id,
        'fecha_inicio' => now(),
        'kilometros_inicio' => $vehiculo->kilometros,
        'id_estado_nafta_inicio' => $vehiculo->id_estado_nafta,
        'id_ultima_ubicacion' => $vehiculo->id_direccion_actual
    ]);

    //  Cambiar estado de reserva
    $reserva->estado = 'en_curso';
    $reserva->save();

    return redirect()->route('viajes.show', $viaje->id)
        ->with('success', 'Viaje iniciado correctamente.');
}


public function finalizarViaje(Request $request, $viajeId)
{
    $viaje = Viaje::with('vehiculo', 'reserva')->findOrFail($viajeId);

    //  Validar que no esté finalizado
    if ($viaje->fecha_fin) {
        return back()->withErrors('El viaje ya fue finalizado.');
    }

    $request->validate([
        'kilometros_fin' => 'required|integer|min:' . $viaje->kilometros_inicio,
        'id_estado_nafta_fin' => 'required|exists:estados_naftas,id',
        'observaciones' => 'nullable|string|max:500'
    ]);

    $vehiculo = $viaje->vehiculo;

    // Actualizar viaje
    if (!$vehiculo->control_satelital && !$request->id_ultima_ubicacion) {
        return back()->withErrors('Debe ingresar ubicación manual.');
    }
    $viaje->update([
        'fecha_fin' => now(),
        'kilometros_fin' => $request->kilometros_fin,
        'id_estado_nafta_fin' => $request->id_estado_nafta_fin,
        'observaciones' => $request->observaciones
    ]);

    //  Actualizar vehículo (estado actual)
    $vehiculo->update([
        'kilometros' => $request->kilometros_fin,
        'id_estado_nafta' => $request->id_estado_nafta_fin,
        'id_direccion_actual' => $viaje->id_ultima_ubicacion
    ]);
    //  Finalizar reserva
    $viaje->reserva->update([
        'estado' => 'finalizada'
    ]);

    return redirect()->route('reservas.index')
        ->with('success', 'Viaje finalizado correctamente.');
}
}
