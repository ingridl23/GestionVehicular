<?php

namespace App\Services;

use App\Models\Reportes;
use App\Enums\EstadoReporte;

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
}

