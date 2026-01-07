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
            ->paginate(20);
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
        return $reporte->load('usuario','comentarios.usuario');
    }

    /**Cambiar estado del reporte */
    public function cambiarEstado(Request $request, Reportes $reporte)
    {
        if (!$request->user()->hasRole('admin')) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'estado' => 'required|in:pendiente,en_revision,atendido,cerrado'
        ]);

        $reporte->update([
            'estado' => $request->estado
        ]);

        return response()->json(['message' => 'Estado actualizado']);
    }

/***************************************************************************************************************** */
    /**Seguimiento de Comentarios en Reportes */

    public function agregarComentario(Request $request, Reportes $reporte)
    {
        $request->validate([
            'comentario' => 'required|string'
        ]);

        $reporte->comentarios()->create([
            'id_usuario' => $request->user()->id,
            'comentario' => $request->comentario
        ]);

        return response()->json(['message' => 'Comentario agregado']);
    }


}
