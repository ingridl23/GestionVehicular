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
            'tipo reserva',
            'fecha reserva',
            'usuario',
            'vehiculo',
            'fecha de inicio de reserva',
            'fecha finalizacion de reserva',
            'estado de la reserva',
            'direccionActual',
            'dependencia duena',
            'dependencia solicitante',
            'creado',
            'modificado'
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
        $reserva->viaje?->direccionActual
      ? $reserva->viaje->direccionActual->calle . ' ' .
        $reserva->viaje->direccionActual->altura . ' - ' .
        $reserva->viaje->direccionActual->ciudad : null,
        $reserva->dependencia_duena?->nombre,
        $reserva->dependencia_solicitante?->nombre,
        $reserva->created_at?->format('Y-m-d'),
        $reserva->updated_at?->format('Y-m-d'),
    ];
}
}
