<?php

namespace App\Http\Controllers;
use App\Models\Reportes;
use App\Services\ReporteService;
use App\Policies\ReportePolicy;
use Illuminate\Http\Request;

class ReporteController extends Controller
{

public function index()
{
    $user = auth()->user();

    if ($user->can('ver_reportes_general')) {
        $reportes = Reportes::all();
    } elseif ($user->can('ver_reportes_dependencia')) {
        $reportes = Reportes::where('dependencia_id', $user->dependencia_id)->get();
    } else {
        $reportes = Reportes::where('usuario_id', $user->id)->get();
    }

    return view('components.reportes', compact('reportes'));
}
/*
    public function index()
    {
         $this->authorize('showReport', Reportes::class);
        return Reportes::with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
*/
    public function store(Request $request, ReporteService $service)
    {
        $this->authorize('createReport', Reportes::class);
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
         $this->authorize('showReport', Reportes::class);
        return $reporte->load('usuario','comentarios.usuario');
    }

    /**Cambiar estado del reporte */
    public function cambiarEstado(Request $request, Reportes $reporte)
    {
         $this->authorize('update', $reporte);

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
      $this->authorize('createMessage', Reportes::class);

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
