<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reportes;

class HistorialController extends Controller{

public function index(){
    return View ('admin.auditoria.index');
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
