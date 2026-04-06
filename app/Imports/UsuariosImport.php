<?php
namespace App\Imports;
use App\Models\User;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Illuminate\Support\Facades\Hash;
class UsuariosImport implements OnEachRow
{
    private $dependencia;
    public $exitosos = 0;
    public $errores = 0;
    public $detalleErrores = [];

    public function __construct($dependencia)
    {
        $this->dependencia = $dependencia;
    }
 private function obtenerRol($valor)
    {
        $roles = [
            1 => 'Administrador General',
            2 => 'Administrador de Dependencia',
            3 => 'Jefe de Area',
            4 => 'Operativo',
        ];

        return $roles[$valor] ?? null;
    }
     public function onRow(Row $row)
    {
        $fila = $row->toArray();

        // Saltar encabezado
        if($row->getIndex() == 1){
            return;
        }

        try {

           $fila = $row->toArray();

           if(empty($fila[0]) || empty($fila[1]) || empty($fila[3])){
                  throw new \Exception("Faltan datos obligatorios");
                                                     }

          if(!filter_var($fila[2], FILTER_VALIDATE_EMAIL)){
                 throw new \Exception("Email inválido");
                        }


         $user = User::updateOrCreate(
                ['legajo' => $fila[3]],
                [
                    'name' => $fila[0],
                    'lastname' => $fila[1],
                    'email' => $fila[2],
                    'id_dependencia' => $this->dependencia->id,
                    'password' => Hash::make('12345678')
                ]
            );


            // Carnet
            if(!empty($fila[4])){
                $user->carnet()->updateOrCreate(
                    ['id_usuario'=>$user->id],
                    [
                        'licencia_de_conducir'=>$fila[4],
                        'fecha_emision'=>$fila[5],
                        'fecha_vencimiento'=>$fila[6]
                    ]
                );
            }

            // Rol
            $rol = $this->obtenerRol((int)$fila[7]);

            if(empty($fila[7])){
                 throw new \Exception("Rol vacío");
                 }

            $user->syncRoles([$rol]);

            $this->exitosos++;

        } catch (\Exception $e) {

            $this->errores++;

            $this->detalleErrores[] = [
                'fila' => $row->getIndex(),
                'error' => $e->getMessage()
            ];
        }
    }

}
