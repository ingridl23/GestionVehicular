<?php
use App\Models\User;
use App\Models\Dependencia;
use Maatwebsite\Excel\Concerns\ToModel;
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

           if(empty($fila['nombre']) || empty($fila['apellido']) || empty($fila['legajo'])){
                  throw new \Exception("Faltan datos obligatorios");
                                                     }

          if(!filter_var($fila['email'], FILTER_VALIDATE_EMAIL)){
                 throw new \Exception("Email inválido");
                        }


         $user = User::updateOrCreate(
                ['legajo' => $fila['legajo']],
                [
                    'name' => $fila['nombre'],
                    'lastname' => $fila['apellido'],
                    'email' => $fila['email'],
                    'id_dependencia' => $this->dependencia->id,

                ]
            );

            if(!$user->wasRecentlyCreated){
    // no tocar password
          } else {
                  $user->password = Hash::make('12345678');
                  $user->save();
                }

            // Carnet
            if(!empty($fila[5])){
                $user->carnet()->updateOrCreate(
                    ['id_usuario'=>$user->id],
                    [
                        'numero'=>$fila['licencia_de_conducir'],
                        'fecha_emision'=>$fila['licencia_emision'],
                        'fecha_vencimiento'=>$fila['licencia_vencimiento']
                    ]
                );
            }

            // Rol
            $rol = $this->obtenerRol((int)$fila['rol']);

            if(empty($fila['rol'])){
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
