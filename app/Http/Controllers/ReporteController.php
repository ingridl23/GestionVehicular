<?php

namespace App\Http\Controllers;

use App\Models\Reportes;
use App\Services\ReporteService;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        return Reportes::with('usuario')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request, ReporteService $service)
    {
        $data = $request->validate([
            'titulo'       => 'required|string',
            'descripcion'  => 'required|string',
            'entidad_tipo' => 'required|string',
            'entidad_id'   => 'required|integer'
        ]);
        $data['id_usuario'] = $request->user()->id;



        return $service->crear($data);
    }

    public function show(Reportes $reporte)
    {
        return $reporte->load('usuario');
    }

    public function cambiarEstado(Request $request, Reportes $reporte, ReporteService $service)
    {
        $request->validate([
            'estado' => 'required|string'
        ]);

        return $service->cambiarEstado($reporte, $request->estado);
    }
}
