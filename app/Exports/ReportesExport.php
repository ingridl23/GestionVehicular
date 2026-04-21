<?php

namespace App\Exports;

use App\Models\Reportes;
use App\Models\ReporteComentarios;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $reportes;

 /*   public function __construct($reportes)
    {
        $this->reportes = $reportes;
    }
*/
   public function collection()
{
    return Reportes::with([
        'usuario',
        'comentarios.usuario'
    ])
    ->where('created_at', '>=', now()->subMonths(3))
    ->get();
}

 public function headings(): array
{
    return [
        'ID',
        'Título',
        'Descripción',
        'Estado',
        'Usuario creador',
        'Usuarios involucrados',
        'Comentarios',
        'Fecha creación',
        'Fecha modificación'
    ];
}
  public function map($reporte): array
{
    // Usuarios involucrados (comentaron)
    $usuarios = $reporte->comentarios
        ->map(fn($c) => $c->usuario?->name)
        ->unique()
        ->implode(', ');

    // Comentarios concatenados con usuario + fecha
    $comentarios = $reporte->comentarios
        ->map(fn($c) =>
            $c->usuario?->name .
            ' (' . $c->created_at->format('d/m H:i') . '): ' .
            $c->comentario
        )
        ->implode(" | ");

    return [
        $reporte->id,
        $reporte->titulo,
        $reporte->descripcion,

        //  FORMATEO DEL ENUM (clave)
        $reporte->estado?->label(),

        $reporte->usuario?->name,

        $usuarios ?: 'Sin participación',

        $comentarios ?: 'Sin comentarios',

        $reporte->created_at?->format('d/m/Y H:i'),
        $reporte->updated_at?->format('d/m/Y H:i'),
    ];
}
}
