<?php

namespace App\Exports;

use App\Models\Reserva;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReservasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $reservas;

    public function __construct($reservas)
    {
        $this->reservas = $reservas;
    }

    public function collection()
    {
        return Reserva::where('created_at', '>=', now()->subMonths(4))
            ->get();
    }

    public function headings(): array
    {
        return [
            'id',
             'tipo_reserva',
            'fecha_reserva',
            'id_usuario',
            'id_vehiculo',
            'fecha_inicio_reserva',
            'fecha_fin_reserva',
            'id_estado_reserva',
            'id_dependencia_duena',
            'id_dependencia_solicitante',
            'created_at',
            'updated_at'
        ];
    }

   public function map($reserva): array
{
    $tipo = $reserva->id_dependencia_duena == $reserva->id_dependencia_solicitante
        ? 'INTERNA'
        : 'EXTERNA';

    return [
        $reserva->id,
        $tipo,
        $reserva->fecha_reserva?->format('Y-m-d H:i'),
        $reserva->usuario?->name,
        $reserva->vehiculo?->dominio,
        $reserva->fecha_inicio_reserva?->format('Y-m-d H:i'),
        $reserva->fecha_fin_reserva?->format('Y-m-d H:i'),
        $reserva->estado_reserva?->estado,
        $reserva->dependencia_duena?->nombre,
        $reserva->dependencia_solicitante?->nombre,
        $reserva->created_at?->format('Y-m-d'),
        $reserva->updated_at?->format('Y-m-d'),
    ];
}
}
