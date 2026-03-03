<?php
namespace App\Http\Controllers;
use App\Models\Viaje;
use App\Models\Reserva;
use App\Models\EstadosReserva;
use App\Services;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\ViajeService;

/**
 * @class ViajeController
 * @brief Controlador encargado de la gestión de viajes asociados a reservas.
 *
 * Permite:
 * - Iniciar viaje
 * - Finalizar viaje
 *
 * Delegación de lógica al ViajeService.
 *
 * @package App\Http\Controllers
 * @since 2026
 */

class ViajeController extends Controller
{
    protected ViajeService $service;

    public function __construct(ViajeService $service)
    {
        $this->service = $service;
    }


    public function index()
{
    $viajes = Viaje::with('vehiculo', 'reserva')
        ->whereHas('reserva', function ($q) {
            $q->where('id_usuario', auth()->id());
        })
        ->latest()
        ->paginate(10);

    return view('ui.viajes.index', compact('viajes'));
}


public function show(Viaje $viaje)
{
    return view('ui.viajes.show', compact('viaje'));
}


/**
 * Inicia un viaje a partir de una reserva.
 *
 * @param int $reservaId Identificador de la reserva.
 * @return \Illuminate\Http\RedirectResponse
 */
    public function comenzarViaje($reservaId)
    {
        $viaje = $this->service->comenzarViaje($reservaId);

        return redirect()->route('operativo.viajes.show', $viaje->id)
            ->with('success', 'Viaje iniciado correctamente.');
    }

    /**
 * Finaliza un viaje activo.
 *
 * Valida:
 * - Kilómetros finales
 * - Estado de nafta final
 * - Observaciones opcionales
 *
 * @param \Illuminate\Http\Request $request
 * @param int $viajeId
 * @return \Illuminate\Http\RedirectResponse
 */
    public function finalizarViaje(Request $request, $viajeId)
    {
        $request->validate([
            'kilometros_fin' => 'required|integer',
            'id_estado_nafta_fin' => 'required|exists:estados_naftas,id',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $this->service->finalizarViaje($viajeId, $request->all());

        return redirect()->route('reservas.internas')
            ->with('success', 'Viaje finalizado correctamente.');
    }
}

