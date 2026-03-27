<?php

namespace App\Exports;

use App\Models\User;

use App\Models\Reserva;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ConductoresExport implements FromCollection, WithHeadings, WithMapping
{
    protected $conductores;

  /*  public function __construct($usuarios)
    {
        $this->usuarios = $usuarios;
    }
*/
    public function collection()
{
    return User::where('created_at', '>=', now()->subMonths(4))
            ->get();
}

    public function headings(): array
    {
        return [

            'conductor',
            'legajo',
            'vehiculo asignado',
            'dependencia_duena',
            'registrado',
            'modificado'
        ];
    }

    public function map($conductor): array
    {
        return [
            $conductor->name,
            $conductor->legajo,



        // Si querés lista:
            $conductor->reservas->last()?->vehiculo?->dominio,
            $conductor->dependencia?->nombre,
            $conductor->created_at?->format('Y-m-d H:i'),
            $conductor->updated_at?->format('Y-m-d H:i'),
        ];
    }
}
