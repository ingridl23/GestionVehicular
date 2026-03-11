<?php

namespace App\Exports;

use App\Models\Gasto;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
class GastosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $gastos;

    public function __construct($gastos)
    {
        $this->gastos = $gastos;
    }

     public function collection()
    {
        return Gasto::with(['viaje','estadoNafta'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->get();
    }

    public function headings(): array
    {
        return [
            'id_gasto',
            'id_viaje',
            'vehiculo',
            'conductor',
            'kilometros',
            'litros_consumidos',
            'precio_litro',
            'monto_total',
            'fecha_calculo'
        ];
    }

    public function map($gasto): array
    {
        return [
            $gasto->id,
            $gasto->viaje?->id,
            $gasto->viaje?->vehiculo?->dominio ?? 'N/A',
            $gasto->viaje?->usuario?->name ?? 'N/A',
            $gasto->kilometros,
            $gasto->litros_consumidos,
            $gasto->precio_litro,
            $gasto->monto,
            $gasto->fecha_calculo?->format('Y-m-d')
        ];
    }
}
