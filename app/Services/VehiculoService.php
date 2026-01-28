<?php

namespace App\Services;

use App\Models\Vehiculo;

use App\Models\EstadosVehiculo;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class VehiculoService
{

    /**
     * Crear vehículo (CU 5)
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

        if ($vehiculo->reservas()->exists() || $vehiculo->viajes()->exists()) {
            throw new Exception('El vehículo tiene reservas o viajes asociados');
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

public function listar(Request $request)
{
    // Captura de filtros desde el Request
    $estadoId      = $request->input('estado_vehiculo_id');
    $dependenciaId = $request->input('dependencia_id');
    $search        = $request->input('search');
   $dependenciaId = $request->input('dependencia_id');
        $estadoId      = $request->input('estado_vehiculo_id');
        $estadoVtv     = $request->input('estado_vtv');
        $sortField     = $request->input('sort_field', 'dominio');
        $sortOrder     = $request->input('sort_order', 'asc');

    $query = Vehiculo::with([
        'dependencia',
        'estado_vehiculo',
        'nafta',
        'direccion'
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
            $query->where('vtv', '<', $hoy);
        } elseif ($estadoVtv === 'por_vencer') {
            $query->whereBetween('vtv', [$hoy, $limite]);
        } elseif ($estadoVtv === 'al_dia') {
            $query->where('vtv', '>', $limite);
        }

        /* ORDEN */
        $allowedSorts = ['dominio', 'marca', 'modelo', 'anio'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'dominio';
        }

        $query->orderBy($sortField, $sortOrder);

        /* PAGINACIÓN */
        $paginator = $query->paginate(20)->appends($request->query());

        /* CLASIFICACIÓN PARA UI */
        $collection = $paginator->getCollection()->map(function ($vehiculo) use ($hoy, $limite) {
            if ($vehiculo->vtv < $hoy) {
                $vehiculo->estado_vtv = 'vencida';
            } elseif ($vehiculo->vtv <= $limite) {
                $vehiculo->estado_vtv = 'por_vencer';
            } else {
                $vehiculo->estado_vtv = 'al_dia';
            }
            return $vehiculo;
        });

        $paginator->setCollection($collection);

        return $paginator;
    }


}





