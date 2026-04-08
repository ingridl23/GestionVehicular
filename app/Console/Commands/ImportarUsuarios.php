<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsuariosImport;
use App\Models\Dependencia;

class ImportarUsuarios extends Command
{
    protected $signature = 'import:usuarios';
    protected $description = 'Importación masiva de usuarios';

    public function handle()
    {
        $ruta = storage_path('app/importaciones/usuarios');
        $procesados = $ruta.'/procesados';

        if(!file_exists($procesados)){
            mkdir($procesados, 0777, true);
        }


        $archivos = scandir($ruta);
        $archivosExcel = array_filter($archivos, function($archivo) use ($ruta) {
            return is_file($ruta.'/'.$archivo) &&
           in_array(pathinfo($archivo, PATHINFO_EXTENSION), ['xlsx','xls']);
        });

       if(count($archivosExcel) === 0){
          $this->warn("No hay archivos Excel para importar");
        return;
        }

        $this->info("Archivos detectados: ".count($archivosExcel));
        $totalExitosos = 0;
        $totalErrores = 0;


        //  BUSQUEDA ROBUSTA (con normalización)
          $dependencias = Dependencia::all();
        foreach ($archivosExcel as $archivo) {

            $this->info("Procesando: $archivo");

             $nombreArchivo = pathinfo($archivo, PATHINFO_FILENAME);

// eliminar prefijo SOLO si es hash
$nombreSinPrefijo = preg_replace('/^[a-z0-9]+_/', '', $nombreArchivo);

// convertir _ a espacios
$nombreDependencia = str_replace('_', ' ', $nombreSinPrefijo);
//buscar con onrmalizacion
    $dependencia = $dependencias->first(function($dep) use ($nombreDependencia) {
        return $this->normalizar($dep->nombre) === $this->normalizar($nombreDependencia);
    });


             if(!$dependencia){
                   $this->error("❌ Dependencia no encontrada: '$nombreDependencia'");

                  rename($ruta.'/'.$archivo, $procesados.'/ERROR_'.uniqid().'_'.$archivo);
                    continue;
              }

            $import = new UsuariosImport($dependencia);

            try {
                  Excel::import($import, $ruta.'/'.$archivo);
              } catch (\Exception $e) {
                   $this->error("❌ Error al importar $archivo: ".$e->getMessage());
                   $totalErrores++;
                   rename($ruta.'/'.$archivo, $procesados.'/ERROR_'.uniqid().'_'.$archivo);
                   continue;
}

            $this->info("✔ Exitosos: ".$import->exitosos);
            $this->error("❌ Errores: ".$import->errores);

            foreach ($import->detalleErrores as $error) {
                $this->error("Fila {$error['fila']}: {$error['error']}");
            }

            $totalExitosos += $import->exitosos;
            $totalErrores += $import->errores;
            $destino = $procesados.'/'.uniqid().'_'.$archivo;
            rename($ruta.'/'.$archivo, $destino);

        }

        $this->info("=================================");
        $this->info("IMPORTACIÓN FINALIZADA");
        $this->info("✔ TOTAL EXITOSOS: $totalExitosos");
        $this->error("❌ TOTAL ERRORES: $totalErrores");
    }


private function normalizar($texto)
{
    $texto = strtolower($texto);
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
    return $texto;
}

    /** comando : php artisan import:usuarios */
}
