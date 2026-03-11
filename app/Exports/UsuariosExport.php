<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsuariosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $usuarios;

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
            'name',
            'lastname',
            'legajo',
            'email',
            'id_dependencia',
            'rol',
            'id_carnet',
            'puede_conducir',
            'cantidad_reservas',
            'created_at',
            'updated_at'
        ];
    }

    public function map($usuario): array
    {
        return [
            $usuario->id,
            $usuario->name,
            $usuario->lastname,
            $usuario->legajo,
            $usuario->email,
            $usuario->dependencia?->id,
            $usuario->roles->first()?->name,
            $usuario->carnet?->id,
            $usuario->puedeConducir() ? 'SI' : 'NO',
            $usuario->reservas->count(),
            $usuario->created_at?->format('Y-m-d'),
            $usuario->updated_at?->format('Y-m-d'),
        ];
    }
}
