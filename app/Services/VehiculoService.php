<?php

namespace App\Services;
use App\Models\Vehiculo;
use App\Models\EstadosVehiculo;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

/**
 * @brief El Siguiente VehiculoService presenta la logica de negocio de vehiculos dentro del sistema
 * @description El service de vehiculos permite tener la logica de craer, modificar,listar y dar de baja un vehiculo dentro del sistema,
 * siguiendo policies y reglas de roles existentes.
 */
class VehiculoService
{

    /**
     * Crear vehículo (CU 5)
     *
     */
    public function crear(array $data): Vehiculo
    {
        return DB::transaction(function () use ($data) {

            if (Vehiculo::where('dominio', $data['dominio'])->exists()) {
                throw new Exception('Ya existe un vehículo con ese dominio');
            }

            // estado inicial al crear
            $data['id_estado_vehiculo'] = $this->estadoId('DISPONIBLE');

            return Vehiculo::create($data);
        });
    }


    /**
     * Actualizar datos del vehículo (CU 4)
     */
    public function actualizar(Vehiculo $vehiculo, array $data): Vehiculo
    {
        // km no puede disminuir
        if (isset($data['kilometros']) && $data['kilometros'] < $vehiculo->kilometros) {
            throw new Exception('Los kilómetros no pueden disminuir');
        }

        // Validar que la nueva VTV NO esté vencida
        if (isset($data['VTV'])&& $data['VTV'])  {
            if (now()->greaterThan($data['VTV'])) {
                throw new Exception('La nueva fecha de VTV no puede estar vencida');
            }
        }

        $vehiculo->update($data);

        return $vehiculo;
    }


    /**
     * Cambiar asignación de dependencia (CU 17)
     */

    public function cambiarAsignacion(Vehiculo $vehiculo, int $dependenciaId): Vehiculo
    {
        if ($vehiculo->estado_vehiculo->estado !== 'DISPONIBLE') {
            throw new Exception('El vehículo no está disponible para reasignar');
        }

        $vehiculo->update([
            'id_dependencia_duena' => $dependenciaId
        ]);

        return $vehiculo;
    }

    /**
     * Eliminar vehículo (CU 3)
     */

    public function eliminar(Vehiculo $vehiculo): void
    {
        $enUso = $this->estadoId('EN_USO');
        $baja  = $this->estadoId('BAJA');
        $enMantenimiento = $this->estadoId('EN_MANTENIMIENTO');

        if ($vehiculo->id_estado_vehiculo === $enUso) {
            throw new Exception('No se puede dar de baja un vehículo en uso');
        }

        if ($vehiculo->id_estado_vehiculo === $baja) {
            throw new Exception('El vehículo ya está dado de baja');
        }

        if ($vehiculo->id_estado_vehiculo === $enMantenimiento){
             throw new Exception ('No se puede utilizar, el vehiculo esta en mantenimiento');
        }

       if (
    $vehiculo->reservas()
        ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
        ->exists()
    ||
    $vehiculo->viajes()
        ->whereIn('estado', ['EN_CURSO'])
        ->exists()
) {
    throw new Exception('El vehículo tiene reservas o viajes activos');
}

        // baja lógica
        $vehiculo->update([
            'id_estado_vehiculo' => $baja
        ]);
    }

  private function estadoId(string $estado): int
{
    $estadoVehiculo = EstadosVehiculo::where('estado', $estado)->first();

    if (!$estadoVehiculo) {
        throw new \Exception("Estado de vehículo no encontrado: {$estado}");
    }

    return $estadoVehiculo->id;
}
/**
 * Metodo para listar vehiculos aplicando filtros de busqueda por estado - dependencia - vtv- dominio - ordenado de reciente y hacia un tiempo.
 */
public function listar(Request $request)
{
    // Captura de filtros desde el Request
    $estadoId      = $request->input('estado_vehiculo_id');
    $dependenciaId = $request->input('dependencia_id');
    $search        = $request->input('search');
    $dependenciaId = $request->input('dependencia_id');
    $estadoVtv     = $request->input('estado_vtv');
    $sortField     = $request->input('sort_field', 'dominio');
    $sortOrder     = $request->input('sort_order', 'asc');

    $query = Vehiculo::with([
    'estadoVehiculo',
    'estadoNafta',
    'dependenciaDuena',
    'direccionActual'
]);



        /* FILTRO DEPENDENCIA */
        if ($dependenciaId) {
            $query->where('id_dependencia_duena', $dependenciaId);
        }

        /* FILTRO ESTADO VEHICULO */
        if ($estadoId) {
            $query->where('id_estado_vehiculo', $estadoId);
        }

        /* BÚSQUEDA GENERAL */
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dominio', 'LIKE', "%{$search}%")
                    ->orWhere('marca', 'LIKE', "%{$search}%")
                    ->orWhere('modelo', 'LIKE', "%{$search}%")
                    ->orWhere('anio', 'LIKE', "%{$search}%");
            });
        }

        /* FILTRO POR ESTADO DE VTV */
        $hoy = now();
        $limite = now()->addDays(30);

        if ($estadoVtv === 'vencida') {
            $query->where('VTV', '<', $hoy);
        } elseif ($estadoVtv === 'por_vencer') {
            $query->whereBetween('VTV', [$hoy, $limite]);
        } elseif ($estadoVtv === 'al_dia') {
            $query->where('VTV', '>', $limite);
        }

        /* ORDEN */
        $allowedSorts = ['dominio', 'marca', 'modelo', 'anio'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'dominio';
        }

        $query->orderBy($sortField, $sortOrder);

        /* PAGINACIÓN */
        $paginator = $query->paginate(20)->appends($request->query());

       /* TRANSFORMAR RESPUESTA PARA QUE SEA COMPATIBLE CON EL FRONTEND */
        $collection = $paginator->getCollection()->map(function ($vehiculo) use ($hoy, $limite) {
            // Clasificar VTV
            if ($vehiculo->VTV < $hoy) {
                $vehiculo->estado_vtv = 'vencida';
            } elseif ($vehiculo->VTV <= $limite) {
                $vehiculo->estado_vtv = 'por_vencer';
            } else {
                $vehiculo->estado_vtv = 'al_dia';
            }

            // CRÍTICO: Convertir relaciones a snake_case para que el JS las entienda
            return [
                'id' => $vehiculo->id,
                'dominio' => $vehiculo->dominio,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'anio' => $vehiculo->anio,
                'kilometros' => $vehiculo->kilometros,
                'VTV' => $vehiculo->VTV,
                'control_satelital' => $vehiculo->control_satelital,
                'gestya_device_id'=> $vehiculo->gestya_device_id,
                'habilitado_prestamo' => $vehiculo->habilitado_prestamo,
                'condiciones_prestamo' => $vehiculo->condiciones_prestamo,
                'id_estado_vehiculo' => $vehiculo->id_estado_vehiculo,
                'id_estado_nafta' => $vehiculo->id_estado_nafta,
                'id_dependencia_duena' => $vehiculo->id_dependencia_duena,
                'id_direccion_actual' => $vehiculo->id_direccion_actual,
                'estado_vtv' => $vehiculo->estado_vtv,

                //  Relaciones en snake_case (como espera el JS)
                'estado_vehiculo' => $vehiculo->estadoVehiculo ? [
                    'id' => $vehiculo->estadoVehiculo->id,
                    'estado' => $vehiculo->estadoVehiculo->estado,
                ] : null,

                'estado_nafta' => $vehiculo->estadoNafta ? [
                    'id' => $vehiculo->estadoNafta->id,
                    'estado' => $vehiculo->estadoNafta->estado,
                ] : null,

                'dependencia_duena' => $vehiculo->dependenciaDuena ? [
                    'id' => $vehiculo->dependenciaDuena->id,
                    'nombre' => $vehiculo->dependenciaDuena->nombre,
                ] : null,

                'direccion_actual' => $vehiculo->direccionActual ? [
                    'id' => $vehiculo->direccionActual->id,
                    'nombre' => $vehiculo->direccionActual->nombre ?? $vehiculo->direccionActual->calle,
                ] : null,
            ];
        });

        $paginator->setCollection(collect($collection));

        return $paginator;

    }


}





