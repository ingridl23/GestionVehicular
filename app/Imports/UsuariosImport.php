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

        // Limpiar espacios en todos los valores
$fila = array_map(fn($v) => is_string($v) ? trim($v) : $v, $fila);

// Si toda la fila está vacía → la salteo
if(count(array_filter($fila)) === 0){
    return;
}
           //$fila = $row->toArray();

           if(empty($fila[0]) || empty($fila[1]) || empty($fila[3])){
                  throw new \Exception("Faltan datos obligatorios");

                                                     }
$email = $fila[2] ?? null;

// limpiar espacios normales
$email = trim($email);

// eliminar caracteres invisibles
$email = preg_replace('/\s+/', '', $email);

// sanitizar
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    throw new \Exception("Email inválido: ".$fila[2]);
}

         $user = User::updateOrCreate(
                ['legajo' => $fila[3]],
                [
                    'name' => $fila[0],
                    'lastname' => $fila[1],
                   'email' => $email,
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
