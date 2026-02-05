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
        $reportes = Reportes::with('usuario', 'comentarios.usuario')->get();
    } elseif ($user->can('ver_reportes_dependencia')) {
        $reportes = Reportes::with('usuario', 'comentarios.usuario')
            ->where('entidad_id', $user->id_dependencia)->get();
    } else {
        $reportes = Reportes::with('usuario', 'comentarios.usuario')
            ->where('id_usuario', $user->id)->get();
    }
  $reportesData = $reportes->map(function ($r) {
        return [
            'id' => $r->id,
            'titulo' => $r->titulo,
            'descripcion' => $r->descripcion,
            'estado' => $r->estado,
            'entidad_tipo' => $r->entidad_tipo,
            'entidad_id' => $r->entidad_id,
            'usuario_id' => $r->usuario->id,
            'usuario_nombre' => $r->usuario->name,
            'fecha' => $r->created_at->format('d/m H:i'),
            'comentarios' => $r->comentarios->map(function ($c) {
                return [
                    'id' => $c->id,
                    'comentario' => $c->comentario,
                    'usuario_id' => $c->usuario->id,
                    'nombre' => $c->usuario->name,
                    'fecha' => $c->created_at->format('d/m H:i'),
                ];
            })->values(),
        ];
    })->values();

  return view('components.reportes', compact('reportes', 'reportesData'));

}

    public function create()
    {

         $this->authorize('iniciar_reporte_interno', Reportes::class);
         return view('ui.miReporte');
    }

   public function store(Request $request, ReporteService $service)
{
    $this->authorize('createReport', Reportes::class);

    $data = $request->validate([
        'titulo'       => 'required|string',
        'descripcion'  => 'required|string',
    ]);

    $data['entidad_tipo'] = 'dependencia';
    $data['entidad_id']   = auth()->user()->id_dependencia;
    $data['id_usuario']   = auth()->id();
    $data['estado']       = 'pendiente';

    $reporte = $service->crear($data);

    return redirect()
        ->route('operativo.reportes.mis', $reporte)
        ->with('success', 'Reporte creado correctamente');
}

   public function show(Reportes $reporte)
{
    $this->authorize('showReport', $reporte);

    return view('components.reportes', [
        'reporte' => $reporte->load('usuario', 'comentarios.usuario')
    ]);
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

   return response()->json([
    'comentario' => [
        'comentario' => $reporte->comentarios->last()->comentario,
        'usuario_id' => auth()->id(),
        'nombre' => auth()->user()->name,
        'fecha' => now()->format('d/m H:i')
    ]
]);


    }


public function misReportes()
{
    $reportes = Reportes::with('usuario', 'comentarios.usuario')
        ->where('id_usuario', auth()->id())
        ->get();

    $reportesData = $reportes->map(fn ($r) => [
        'id' => $r->id,
        'titulo' => $r->titulo,
        'descripcion' => $r->descripcion,
        'estado' => $r->estado,
        'entidad_tipo' => $r->entidad_tipo,
        'entidad_id' => $r->entidad_id,
        'usuario_id' => $r->usuario->id,
        'usuario_nombre' => $r->usuario->name,
        'fecha' => $r->created_at->format('d/m H:i'),
        'comentarios' => $r->comentarios->map(fn ($c) => [
            'comentario' => $c->comentario,
            'usuario_id' => $c->usuario->id,
            'nombre' => $c->usuario->name,
            'fecha' => $c->created_at->format('d/m H:i'),
        ])->values()
    ])->values();

    return view('components.reportes', compact('reportes', 'reportesData'));
}

// OPERATIVO (mobile)
public function misReportesOperativo()
{
    $reportes = Reportes::where('id_usuario', auth()->id())
        ->latest()
        ->get();

    return view('operativo.reportes.index', compact('reportes'));
}



}
