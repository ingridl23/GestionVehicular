<?php
use App\Models\User;
use App\Models\Dependencia;
use Maatwebsite\Excel\Concerns\ToModel;

class UsuariosImport implements ToModel
{
    private $dependencia;

    public function __construct($dependencia)
    {
        $this->dependencia = $dependencia;
    }

   public function model(array $row)
    {
        return User::updateOrCreate(
            ['legajo' => $row['legajo']],
            [
                'name' => $row['nombre'],
                'lastname' => $row['apellido'],
                'email' => $row['email'],
                'id_dependencia' => $this->dependencia->id,
                'password' => bcrypt('12345678')
            ]
        );
    }
}
