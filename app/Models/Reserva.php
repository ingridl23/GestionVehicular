<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Reserva extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'reservas';

    protected $fillable = [
        'fecha_reserva',
        'fecha_inicio_reserva',
        'fecha_fin_reserva',
        'id_vehiculo',
        'id_estado_reserva',
        'id_dependencia_duena',
        'id_dependencia_solicitante',
        'id_usuario'
    ];
    protected $casts = [
        'fecha_reserva' => 'datetime',
        'fecha_inicio_reserva' => 'datetime',
        'fecha_fin_reserva' => 'datetime',
    ];


    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function estado_reserva()
    {
        return $this->belongsTo(EstadosReserva::class, 'id_estado_reserva');
    }

    public function dependencia_duena()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_duena');
    }

    public function dependencia_solicitante()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_solicitante');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function viaje()
    {
        return $this->hasMany(Viaje::class);
    }

    // Obtiene:
    //Casos que involucran la dependencia del usuario con dependencias internas
    // independencias hijas involucradas
    // id_dependencia -> hija
    // hija -> id_dependencia
    // hija -> hija
    // nieja -> hija

    public function scopeObtenerDependenciasInternas($query, $id_dependencia)
    {
        $dependencia = Dependencia::with('dependenciasHijas')->find($id_dependencia);

        if (!$dependencia) {
            return $query->whereRaw('1 = 0');
        }

        // Se obtienen todsos los ID`s (el de la dependencia padre + todos sus hijos)
        $idsPermitidos = array_merge(
            [$dependencia->id],
            $dependencia->obtenerIdsHijas()
        );

        return $query
            // reservas internas
            ->whereIn('id_dependencia_duena', $idsPermitidos)

            // solicitante puede ser cualquiera que pertenezca al árbol de la dependencia
            ->whereIn('id_dependencia_solicitante', $idsPermitidos);
    }

    // Obtiene:
    // Reservas que involucran a la dependencia ($id_dependencia)
    //Reservas que involucran a las hijas (ya sea solicitante o dueña)
    // Contempla casos donde se tienen nietos
    // id_dependencia -> externa
    // externa -> id_dependencia
    // externa -> hija
    // hija -> externa
    // nieja -> externa

    public function scopeObtenerDependenciasExternas($query, $id_dependencia)
    {
        $dependencia = Dependencia::with('dependenciasHijas')->find($id_dependencia);

        if (!$dependencia) {
            return $query->whereRaw('1 = 0');
        }

        $idsArbol = array_merge(
            [$dependencia->id],
            $dependencia->obtenerIdsHijas()
        );

        return $query->where(function ($q) use ($idsArbol) {

            // Yo (o mis hijas) soy dueño y el solicitante es externo
            $q->where(function ($q1) use ($idsArbol) {
                $q1->whereIn('id_dependencia_duena', $idsArbol)
                    ->whereNotIn('id_dependencia_solicitante', $idsArbol);
            })

                // O yo (o mis hijas) soy solicitante y el dueño es externo
                ->orWhere(function ($q1) use ($idsArbol) {
                    $q1->whereIn('id_dependencia_solicitante', $idsArbol)
                        ->whereNotIn('id_dependencia_duena', $idsArbol);
                });
        });
    }


    public function scopeObtenerDependenciasExternasPendientes($query, $id_dependencia)
    {
        $dependencia = Dependencia::with('dependenciasHijas')->find($id_dependencia);

        if (!$dependencia) {
            return $query->whereRaw('1 = 0');
        }

        $idsArbol = array_merge(
            [$dependencia->id],
            $dependencia->obtenerIdsHijas()
        );

        $query->where(function ($q) use ($idsArbol) {

            // Yo (o mis hijas) soy dueño y el solicitante es externo
            $q->where(function ($q1) use ($idsArbol) {
                $q1->whereIn('id_dependencia_duena', $idsArbol)
                    ->whereNotIn('id_dependencia_solicitante', $idsArbol);
            });
        });


        return $query->whereIn('id_estado_reserva', function ($sub) {
            $sub->select('id')
                ->from('estados_reservas')
                ->whereIn('estado', ['PENDIENTE']);
        });
    }

    /**
     * Scope que filtra únicamente las reservas internas.
     *
     * Se considera una reserva interna cuando la dependencia dueña del vehículo
     * y la dependencia solicitante pertenecen al mismo árbol jerárquico,
     * independientemente del nivel (misma dependencia, hija, nieta, bisnieta, etc.).
     *
     * Criterios aplicados:
     * - La dependencia dueña y la solicitante son la misma.
     * - La dependencia solicitante pertenece al árbol completo de la dependencia dueña.
     * - La dependencia dueña pertenece al árbol completo de la dependencia solicitante.
     *
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
    */
   public function scopeSoloInternas($query){

    $dependencias = \App\Models\Dependencia::all();

    $mapaArboles = [];

    foreach ($dependencias as $dep) {
        $mapaArboles[$dep->id] = array_merge(
            [$dep->id],
            $dep->obtenerIdsHijas()
        );
    }

    return $query->where(function ($q) use ($mapaArboles) {

        $q->whereColumn(
            'id_dependencia_duena',
            'id_dependencia_solicitante'
        );

        foreach ($mapaArboles as $ids) {
            $q->orWhere(function ($sub) use ($ids) {
                $sub->whereIn('id_dependencia_duena', $ids)
                    ->whereIn('id_dependencia_solicitante', $ids);
            });
        }

    });
}


    /**
 * Scope que filtra únicamente las reservas externas.
 *
 * Se considera una reserva externa cuando la dependencia dueña del vehículo
 * y la dependencia solicitante NO pertenecen al mismo árbol jerárquico,
 * es decir, no existe relación directa ni indirecta entre ellas
 * (no son la misma, ni hijas, ni nietas, ni bisnietas, etc.).
 *
 * Criterios aplicados:
 * - La dependencia dueña y la solicitante son distintas.
 * - La dependencia solicitante NO pertenece al árbol de la dueña.
 * - La dependencia dueña NO pertenece al árbol  de la solicitante.
 *
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @return \Illuminate\Database\Eloquent\Builder
 */
   public function scopeSoloExternas($query){
    return $query
        ->whereColumn('reserva.id_dependencia_duena', '!=', 'reserva.id_dependencia_solicitante')

        // La solicitante NO es hija directa de la dueña
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('dependencias as d1')
              ->whereColumn('d1.id', 'reserva.id_dependencia_solicitante')
              ->whereColumn('d1.id_dependencia_padre', 'reserva.id_dependencia_duena');
        })

        // La dueña NO es hija directa de la solicitante
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('dependencias as d2')
              ->whereColumn('d2.id', 'reserva.id_dependencia_duena')
              ->whereColumn('d2.id_dependencia_padre', 'reserva.id_dependencia_solicitante');
        });
}


    public function scopePendientes($query){
        return $query->whereHas('estado_reserva', function ($q) {
            $q->where('estado', 'PENDIENTE');
        });
    }
}
