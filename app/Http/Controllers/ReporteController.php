<?php

namespace App\Http\Controllers;
use App\Models\Reportes;
use App\Models\User;
use App\Models\Alerta;
use App\Services\ReporteService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Enums\EstadoReporte;
use App\Notifications\UsuarioModificadoNotification;

/**
 * @class ReporteController
 * @brief Controlador encargado de la gestión integral de reportes internos.
 *
 * Permite:
 * - Listado de reportes según permisos del usuario
 * - Creación de nuevos reportes
 * - Visualización individual
 * - Cambio de estado
 * - Seguimiento mediante comentarios
 * - Eliminación controlada
 * - Vistas específicas para operativo (mobile)
 *
 * Implementa control de acceso basado en permisos
 * y utiliza ReporteService para la lógica de negocio.
 *
 * Integra sistema de notificaciones al modificar estado
 * o agregar comentarios.
 *
 * @package App\Http\Controllers
 * @author Ingrid Ledesma
 * @version 1.0
 * @since 2026
 */

class ReporteController extends Controller
{

/**
 * Muestra el listado de reportes según el nivel de acceso del usuario.
 *
 * - Permiso general: visualiza todos los reportes.
 * - Permiso por dependencia: visualiza reportes de su entidad.
 * - Usuario estándar operativo: visualiza únicamente sus propios reportes.
 *
 * Incluye relación con usuario y comentarios.
 *
 * @return \Illuminate\View\View
 */

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
            'id_usuario' => $r->usuario->id,
            'usuario_nombre' => $r->usuario->name,
            'fecha' => $r->created_at->format('d/m H:i'),
            'comentarios' => $r->comentarios->map(function ($c) {
                return [
                    'id' => $c->id,
                    'comentario' => $c->comentario,
                    'id_usuario' => $c->usuario->id,
                    'nombre' => $c->usuario->name,
                    'fecha' => $c->created_at->format('d/m H:i'),
                ];
            })->values(),
        ];
    })->values();

  return view('components.reportes', compact('reportes', 'reportesData'));

}

/**
 * Muestra el formulario de creación de reportes.
 *
 * Requiere autorización mediante la policy
 * 'iniciar_reporte_interno'.
 *
 * @return \Illuminate\View\View
 * @throws \Illuminate\Auth\Access\AuthorizationException
 */

    public function create()
    {

         $this->authorize('iniciar_reporte_interno', Reportes::class);
         return view('operativo.reportes.createReporte');
    }


    /**
 * Crea un nuevo reporte interno.
 *
 * Valida título y descripción.
 * Asigna automáticamente:
 * - entidad_tipo
 * - entidad_id
 * - id_usuario
 * - estado inicial (pendiente)
 *
 * Delegación de persistencia al ReporteService.
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Services\ReporteService $service
 * @return \Illuminate\Http\RedirectResponse
 */

   public function store(Request $request, ReporteService $service)
{
    //$this->authorize('createReport', Reportes::class);
    //Verificación manual temporal
    if (!auth()->user()->hasPermissionTo('iniciar_reporte_interno')) {
        abort(403, 'No tenés permiso para crear reportes');
    }

    $data = $request->validate([
        'titulo'       => 'required|string',
        'descripcion'  => 'required|string',
    ]);

    $data['entidad_tipo'] = 'dependencia';
    $data['entidad_id']   = auth()->user()->id_dependencia;
    $data['id_usuario']   = auth()->id();
    $data['estado']       = 'pendiente';

    $reporte = $service->crear($data);

   //  Notificar a administradores de esa dependencia
   $dependenciaId = auth()->user()->id_dependencia;

$admins = User::where('id_dependencia', $dependenciaId)
    ->whereHas('roles', function ($q) {
        $q->whereIn('name', [
            'Administrador General',
            'Administrador de Dependencia',
            'Administrador de Área'
        ]);
    })
    ->get();

foreach ($admins as $admin) {
    $admin->notify(new UsuarioModificadoNotification(
        'Nuevo reporte creado por ' . auth()->user()->name . ': ' . $reporte->titulo,
        'info'
    ));
}


    return redirect()
        ->route('operativo.reportes.index')
        ->with('success', 'Reporte creado correctamente');
}

/**
 * Muestra el detalle de un reporte específico.
 *
 * Incluye comentarios asociados y datos del usuario.
 *
 * @param \App\Models\Reportes $reporte
 * @return \Illuminate\View\View
 */


public function show(Reportes $reporte)
{
   // $this->authorize('view', $reporte);
    $reporte->load('comentarios.usuario');
$reportesData = [[
        'id' => $reporte->id,
        'titulo' => $reporte->titulo,
        'descripcion' => $reporte->descripcion,
        'estado' => $reporte->estado,
        'entidad_tipo' => $reporte->entidad_tipo,
        'entidad_id' => $reporte->entidad_id,
       'id_usuario' => $reporte->id_usuario,
        'usuario_nombre' => $reporte->usuario->name,
        'fecha' => $reporte->created_at->format('d/m H:i'),
        'comentarios' => $reporte->comentarios->map(fn ($c) => [
            'comentario' => $c->comentario,
            'id_usuario' => $c->usuario->id,
            'nombre' => $c->usuario->name,
            'fecha' => $c->created_at->format('d/m H:i'),
        ])->values()
    ]];

    return view('operativo.reportes.show', compact('reporte', 'reportesData'));

}





/**
 * Cambia el estado de un reporte.
 *
 * Valida que el estado pertenezca al Enum EstadoReporte.
 * Si el reporte se marca como ATENDIDO,
 * envía notificación al usuario creador.
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Models\Reportes $reporte
 * @return \Illuminate\Http\JsonResponse
 */
public function cambiarEstado(Request $request, Reportes $reporte)
{


    //$this->authorize('update', $reporte);

    //  VALIDACIÓN CORRECTA usando los valores del enum
 $request->validate([
    'estado' => ['required', Rule::in(EstadoReporte::values())],
]);


    $reporte->update([
        'estado' => $request->estado
    ]);

    if ($request->estado === EstadoReporte::ATENDIDO->value) {
    $reporte->usuario->notify(
        new UsuarioModificadoNotification(
            'Tu reporte fue marcado como resuelto',
            'info'
        )
    );
}

    return response()->json([
        'success' => true,
        'message' => 'Estado actualizado correctamente',
        'nuevo_estado' => $reporte->estado
    ]);
}


/***************************************************************************************************************** */
  /**
 * Agrega un comentario a un reporte.
 *
 * Valida que el comentario no esté vacío.
 * Crea el registro asociado al usuario autenticado.
 * Notifica al creador del reporte si quien comenta es otro usuario.
 *
 * @param \Illuminate\Http\Request $request
 * @param \App\Models\Reportes $reporte
 * @return \Illuminate\Http\JsonResponse
 */


/**Seguimiento de Comentarios en Reportes */
// Quedo en conocimiento que esta funcionalidad queda sin policy hasta que se resuelva que no reconozce el permiso
    public function agregarComentario(Request $request, Reportes $reporte)
    {
     // $this->authorize('commet', $reporte);


       $request->validate([
        'comentario' => 'required|string'
    ]);

    $reporte->comentarios()->create([
        'id_usuario' => $request->user()->id,
        'comentario' => $request->comentario
    ]);

//  Notificar solo si quien comenta NO es el creador
    if ($reporte->id_usuario !== auth()->id()) {
        $reporte->usuario->notify(
            new UsuarioModificadoNotification(
                'Tu reporte recibió una nueva respuesta',
                'success'
            )
        );
    }

   return response()->json([
    'comentario' => [
        'comentario' => $reporte->comentarios->last()->comentario,
        'id_usuario' => auth()->id(),
        'nombre' => auth()->user()->name,
        'fecha' => now()->format('d/m H:i')
    ]
]);


    }

/**
 * Muestra los reportes creados por el usuario autenticado.
 *
 * Incluye comentarios y datos del usuario.
 *
 * @return \Illuminate\View\View
 */
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
        'id_usuario' => $r->usuario->id,
        'usuario_nombre' => $r->usuario->name,
        'fecha' => $r->created_at->format('d/m H:i'),
        'comentarios' => $r->comentarios->map(fn ($c) => [
            'comentario' => $c->comentario,
            'id_usuario' => $c->usuario->id,
            'nombre' => $c->usuario->name,
            'fecha' => $c->created_at->format('d/m H:i'),
        ])->values()
    ])->values();

    return view('components.reportes', compact('reportes', 'reportesData'));
}

/**
 * Muestra los reportes del usuario autenticado
 * en vista operativa (mobile).
 *
 * Incluye las últimas alertas registradas.
 *
 * @return \Illuminate\View\View
 */
// OPERATIVO (mobile)
public function misReportesOperativo()
{
    $reportes = Reportes::where('id_usuario', auth()->id())
        ->latest()
        ->get();

    $alertas = Alerta::latest()->take(10)->get();

    return view('operativo.reportes.index', compact('reportes', 'alertas'));
}
/**
 * Elimina un reporte.
 *
 * Solo permite eliminar reportes en estado cerrado.
 * Devuelve error 403 si el estado no es válido.
 *
 * @param int $id Identificador del reporte.
 * @return \Illuminate\Http\JsonResponse
 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
 */
public function deleteReporte($id){
$reporte = Reportes::findOrFail($id);

    if (!$reporte->isCerrado()) {
        return response()->json([
            'message' => 'Solo se pueden eliminar reportes cerrados'
        ], 403);
    }

    $reporte->delete();

    return response()->json([
        'message' => 'Reporte eliminado correctamente'
    ]);

}

}
