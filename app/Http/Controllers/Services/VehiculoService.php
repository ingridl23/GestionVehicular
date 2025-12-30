<?php

namespace App\Services;

use App\Models\Vehiculo;
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

            return Vehiculo::create($data);
        });
    }

    /**
     * Actualizar datos del vehículo (CU 4)
     */
    public function actualizar(Vehiculo $vehiculo, array $data): Vehiculo
    {
        if (isset($data['VTV']) && now()->greaterThan($data['VTV'])) {
            throw new Exception('La VTV está vencida');
        }

        $vehiculo->update($data);

        return $vehiculo;
    }

    /**
     * Cambiar asignación de dependencia (CU 17)
     */
    public function cambiarAsignacion(Vehiculo $vehiculo, int $dependenciaId): Vehiculo
    {
        if ($vehiculo->estado_vehiculo->nombre !== 'Disponible') {
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
        if ($vehiculo->reservas()->exists() || $vehiculo->viajes()->exists()) {
            throw new Exception('El vehículo tiene reservas o viajes asociados');
        }

        $vehiculo->delete();
    }


    public function listar(Request $request)
    {
        $search        = $request->input('search');
        $dependenciaId = $request->input('dependencia_id');
        $estadoId      = $request->input('estado_vehiculo_id');
        $sortField     = $request->input('sort_field', 'dominio');
        $sortOrder     = $request->input('sort_order', 'asc');

        $query = Vehiculo::with([
            'dependencia',
            'estado_vehiculo',
            'nafta',
            'direccion'
        ]);

        /* ----------------------
         FILTRO POR DEPENDENCIA
        ---------------------- */
        if ($dependenciaId) {
            $query->where('id_dependencia_duena', $dependenciaId);
        }

        /* ----------------------
         FILTRO POR ESTADO
        ---------------------- */
        if ($estadoId) {
            $query->where('id_estado_vehiculo', $estadoId);
        }

        /* ----------------------
         BÚSQUEDA GENERAL
        ---------------------- */
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dominio', 'LIKE', "%{$search}%")
                    ->orWhere('marca', 'LIKE', "%{$search}%")
                    ->orWhere('modelo', 'LIKE', "%{$search}%")
                    ->orWhere('anio', 'LIKE', "%{$search}%");
            });
        }

        /* ----------------------
         ORDENAMIENTO SEGURO
        ---------------------- */
        $allowedSorts = ['dominio', 'marca', 'modelo', 'anio'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'dominio';
        }

        $query->orderBy($sortField, $sortOrder);

        /* ----------------------
         PAGINACIÓN
        ---------------------- */
        return $query->paginate(20)->appends($request->query());
    }
}
