<?php

namespace App\Exports;

use App\Models\User;
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
    return User::all();
}

    public function headings(): array
    {
        return [
            'id',

            'created_at',
            'updated_at'
        ];
    }

    public function map($conductor): array
    {
        return [
            $conductor->id,
            $conductor->created_at?->format('Y-m-d'),
            $conductor->updated_at?->format('Y-m-d'),
        ];
    }
}
