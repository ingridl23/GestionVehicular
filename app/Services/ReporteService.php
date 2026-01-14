<?php

namespace App\Services;

use App\Models\Reportes;
use App\Models\Alerta;
use App\Enums\EstadoReporte;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
class ReporteService
{
public function crear(array $data): Reportes
{
return Reportes::create([
'titulo' => $data['titulo'],
'descripcion' => $data['descripcion'],
'id_usuario' => $data['id_usuario'],
'entidad_tipo' => $data['entidad_tipo'],
'entidad_id' => $data['entidad_id'],
'estado' => EstadoReporte::PENDIENTE,
]);
}

public function cambiarEstado(Reportes $reporte, string $estado): Reportes
{
$reporte->update([
'estado' => $estado
]);

return $reporte;
}


    /** Reporte generado automáticamente desde una alerta */
    public function crearReporteDesdeAlerta(Alerta $alerta): void
    {
        Reportes::create([
            'titulo' => 'Reporte automático por alerta',
            'descripcion' => $alerta->mensaje,
            'entidad_tipo' => $alerta->entidad_tipo,
            'entidad_id' => $alerta->entidad_id,
            'estado' => EstadoReporte::PENDIENTE,
            'id_usuario' => null
        ]);
    }



    public function asignarReporte(Reportes $reporte)
    {
        $reporte->update([
            'id_usuario' => auth()->id(),
            'estado' => EstadoReporte::EN_REVISION,
        ]);
    }

    /** filtrado basico de reportes*/


    public function filtrar(array $filtros)
    {
        return Reportes::query()
            ->when($filtros['estado'] ?? null, function (Builder $q, $estado) {
                $q->where('estado', $estado);
            })
            ->when($filtros['entidad_tipo'] ?? null, function (Builder $q, $tipo) {
                $q->where('entidad_tipo', $tipo);
            })
            ->when($filtros['fecha_desde'] ?? null, function (Builder $q, $desde) {
                $q->whereDate('created_at', '>=', $desde);
            })
            ->when($filtros['fecha_hasta'] ?? null, function (Builder $q, $hasta) {
                $q->whereDate('created_at', '<=', $hasta);
            })
            ->with(['usuario'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

