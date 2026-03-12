<?php

namespace App\Exports;
use App\Models\Vehiculo;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class VehiculosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $vehiculos;

  /*  public function __construct($vehiculos)
    {
        $this->vehiculos = $vehiculos;
    }*/

  public function collection()
    {
        return Vehiculo::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'dominio',
            'marca',
            'modelo',
            'anio',
            'direccion actual',
            'estado vehiculo',
            'dependencia duena',
            'estado del combustible',
            'control satelital',
            'habilitado para prestamo',
            'condiciones de prestamo',
            'kilometros',
            'vtv',
            'cantidad de viajes',
            'registrado',
            'modificado'
        ];
    }

    public function map($vehiculo): array
    {
        return [
            $vehiculo->id,
            $vehiculo->dominio,
            $vehiculo->marca,
            $vehiculo->modelo,
            $vehiculo->anio,
            $vehiculo->direccionActual?->id,
            $vehiculo->estadoVehiculo?->id,
            $vehiculo->dependenciaDuena?->id,
            $vehiculo->estadoNafta?->id,
            $vehiculo->control_satelital,
            $vehiculo->habilitado_prestamo,
            $vehiculo->condiciones_prestamo,
            $vehiculo->kilometros,
            $vehiculo->vtv?->format('Y-m-d'),
            $vehiculo->viajes->count(),
            $vehiculo->created_at?->format('Y-m-d'),
            $vehiculo->updated_at?->format('Y-m-d'),
        ];
    }
}
