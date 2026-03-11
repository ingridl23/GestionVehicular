<?php

namespace App\Exports;
use App\Models\Viaje;
use App\Models\EstadosViaje;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ViajesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $viajes;

    public function __construct($viajes)
    {
        $this->viajes = $viajes;
    }

    public function collection()
    {
        return $this->viajes;
    }

    public function headings(): array
    {
        return [
            'id',
            'id_reserva',
            'id_vehiculo',
            'fecha_inicio',
            'fecha_fin',
            'kilometros_inicio',
            'kilometros_fin',
            'id_estado_nafta_inicio',
            'id_estado_nafta_fin',
            'ultima_latitud',
            'ultima_longitud',
            'observaciones',
            'created_at',
            'updated_at'
        ];
    }

    public function map($viaje): array
    {
        return [
            $viaje->id,
            $viaje->id_reserva,
            $viaje->id_vehiculo,
            $viaje->fecha_inicio,
            $viaje->fecha_fin,
            $viaje->kilometros_inicio,
            $viaje->kilometros_fin,
            $viaje->id_estado_nafta_inicio,
            $viaje->id_estado_nafta_fin,
            $viaje->ultimaCoordenada?->latitud,
            $viaje->ultimaCoordenada?->longitud,
            $viaje->observaciones,
            $viaje->created_at?->format('Y-m-d'),
            $viaje->updated_at?->format('Y-m-d'),
        ];
    }

}

