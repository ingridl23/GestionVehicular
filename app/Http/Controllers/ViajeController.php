<?php
namespace App\Http\Controllers;
use App\Models\Viaje;
use App\Models\Reserva;
use App\Models\EstadosNafta;
use App\Models\Direcciones;
use Illuminate\Http\Request;
use App\Exports\ViajesExport;
use Maatwebsite\Excel\Facades\Excel;
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

    public function __construct(ViajeService $service )
    {
        $this->service = $service;



    }

    /**
     * Listado de viajes.
     * - Admin/Jefe: ve todos los viajes de su dependencia
     * - Operativo:  ve solo sus propios viajes
     * Además resuelve $reservaActiva y $viajeActivo para los botones del dashboard.
     */
    public function index()
    {

        $user = auth()->user();

        // ── Viajes a mostrar según rol ──

        $query = Viaje::with(['vehiculo', 'reserva.usuario','ultimaCoordenada', 'reserva.estado_reserva'])
            ->latest();

        if ($user->hasRole('Operativo')) {
            // Solo sus viajes
            $query->whereHas('reserva', fn($q) => $q->where('id_usuario', $user->id));
        } elseif ($user->hasAnyRole(['Administrador de Dependencia', 'Jefe de Area'])) {
            // Viajes de su dependencia
            $query->whereHas('reserva', function ($q) use ($user) {
                $q->where('id_dependencia_duena', $user->dependencia?->id);
            });
        }
        // Administrador General: ve todos (sin filtro adicional)

     $viajes = $query->paginate(10);

        // ── Reserva aprobada pendiente de iniciar (solo relevante para Operativo) ──
     $reservaActiva = Reserva::with('vehiculo')
    ->where('id_usuario', $user->id)
    ->whereHas('estado_reserva', fn($q) => $q->where('estado', 'APROBADA'))
    ->whereNotExists(function ($q) {
        $q->select('id')
          ->from('viaje')
          ->whereColumn('viaje.id_reserva', 'reserva.id')
          ->whereNull('viaje.fecha_fin');
    })
    ->first();
        // ── Estados de nafta para el modal de finalizar ──
        $estadosNafta = EstadosNafta::all();

        // ── Variables para filtros (admins) ──
        $vehiculos_filtros = \App\Models\Vehiculo::select('id', 'dominio')->orderBy('dominio')->get();
        $estados_filtros   = \App\Models\EstadosViaje::all();


        $direcciones = Direcciones::all();


        return view('ui.viajes.index', compact(
            'viajes',
            'reservaActiva',
            'estadosNafta',
            'vehiculos_filtros',
            'estados_filtros',
            'direcciones'
        ));
    }

    /**
     * Detalle de un viaje.
     * Permite que al seleccionar un viaje se pueda visualizar la informacion vinculada al mismo.
     * Detalles: dependencia de origen,usuario asignado,estado del combustible al iniciar el recorrido,estado del combustible al finalizar el recorrido,
     * gasto obtenido una vez finalizado el viaje y la ubicacion actual del vehiculo una vez finalizada la reserva.
     */
   public function show(Viaje $viaje)
{
    $viaje->load([
        'vehiculo.dependenciaDuena',
        'reserva.usuario',
        'estadoNaftaInicio',
        'estadoNaftaFin',
        'gasto',
        'ultimaCoordenada',

    ]);

    $estadosNafta = EstadosNafta::all();
    $direcciones = Direcciones::all();
    return view('ui.viajes.show', compact('viaje', 'estadosNafta','direcciones'));
}

public function mapaVirtual(Viaje $viaje)
{
    $viaje->load('ultimaCoordenada');

    return view('ui.viajes.mapa-virtual', compact('viaje'));
}
/**
 * Iniciar un viaje a partir de una reserva.
 *
 * @param int $reservaId Identificador de la reserva.
 * @return \Illuminate\Http\RedirectResponse
 */


   public function comenzarViaje($reservaId)
{

    try {
        $viaje = $this->service->comenzarViaje($reservaId);

        return redirect()->route('viajes.mapa', $viaje->id)
            ->with('success', 'Viaje iniciado.');

    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}

/*
    try {
        $viaje = $this->service->comenzarViaje($reservaId);
        return redirect()->route('operativo.viajes.index')
            ->with('success', 'Viaje #' . $viaje->id . ' iniciado.');
    }  catch (\Illuminate\Validation\ValidationException $e) {
        dd(['ValidationException' => $e->errors()]);
    } catch (\Exception $e) {
        return redirect()->back()
            ->withErrors(['error' => $e->getMessage()]);
    }
}
*/

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
            'kilometros_fin'      => 'required|integer|min:0',
            'id_estado_nafta_fin' => 'required|exists:estados_naftas,id',
            'observaciones'       => 'nullable|string|max:500',
              'id_direccion'        => 'nullable|exists:direccion,id'
        ]);

        $this->service->finalizarViaje((int) $viajeId, $request->all());

        return redirect()->route('operativo.viajes.index')
            ->with('success', 'Viaje finalizado correctamente.');
    }


    /**
     * EXPORTAR DATOS A FORMATO EXCEL:
     * Metodo para exportar historial de viajes a formato excel fuera del sistema registrados durante los ultimos 4 meses.
     *  Uso administrativo
     *
     * @todo "Queda pendiente imnplementar un filtro por fechas para exportaciones, por el momento solo es generado por el sistema el registro de
     * los ultimos 4 meses y en gastos ultimos 6 meses"
     */

public function export()
{
    return Excel::download(
        new ViajesExport,
        'viajes_ultimos_4_meses.xlsx'
    );
}
}

