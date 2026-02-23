<?php
namespace App\Http\Controllers;
use App\Models\Viaje;
use App\Models\Reserva;
use App\Models\EstadosReserva;
use App\Services\Viajes\ViajeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ViajeController extends Controller
{
    protected ViajeService $service;

    public function __construct(ViajeService $service)
    {
        $this->service = $service;
    }

    public function comenzarViaje($reservaId)
    {
        $viaje = $this->service->comenzarViaje($reservaId);

        return redirect()->route('viajes.show', $viaje->id)
            ->with('success', 'Viaje iniciado correctamente.');
    }

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

