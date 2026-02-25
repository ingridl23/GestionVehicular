<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Reportes;
use App\Models\Reserva;
use App\Models\Vehiculo;
class HistorialController extends Controller{

public function index(){
     $ultimosVehiculos = Vehiculo::with('estadoVehiculo')
        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();


      $vehiculosStats = [
    $total = Vehiculo::count(),
    $disponibles = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'DISPONIBLE')
    )->count(),
    $reservados = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'EN_USO')
    )->count(),


    $mantenimiento = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'EN_MANTENIMIENTO')
    )->count(),
    $baja = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'BAJA'))->count()
];


    return View ('admin.auditoria.index', compact('ultimosVehiculos','ultimasReservas','total','disponibles','reservados','reportesp','reportesA','mantenimiento','baja'));
}

    public function resumen()
    {
        return Reportes::select('estado')
            ->selectRaw('count(*) as total')
            ->groupBy('estado')
            ->get();
    }


    // VTV – listado
    public function listarVtv(Request $request)
    {
        // TODO: implementar
        // filtros: dependencia, estado_vtv, search, paginación
    }

    // VTV – resumen (para dashboard)
    public function resumenVtv(Request $request)
    {
        // TODO: implementar
        // devolver: al_dia, por_vencer, vencida
    }

    // (futuros)
    // public function listarMantenimientos() { ... }
}
