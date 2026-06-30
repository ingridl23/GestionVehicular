<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
/**
 * @class Reserva
 *
 * Representa una categoría dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property date $fecha_reserva
 * @property date $fecha_inicio_reserva
 * @property date $fecha_fin_reserva
 * @property int $id_vehiculo
 * @property int $id_estado_reserva
 * @property  int $id_dependencia_duena
 * @property int $id_dependencia_solicitante
 * @property int $id_usuario
 * @property string $estado  estado del reporte generado
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Reserva extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'reserva';

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
/**
 * Relaciones
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 * @param int $id
 *
 */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    /**
     * Relaciones
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @param int $id
     *
     */
    public function estado_reserva()
    {
        return $this->belongsTo(EstadosReserva::class, 'id_estado_reserva');
    }
    /**
     * Relaciones
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @param int $id
     *
     */

    public function dependencia_duena()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_duena');
    }

    /**
     * Relaciones
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @param int $id
     *
     */
    public function dependencia_solicitante()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_solicitante');
    }
    /**
     * Relaciones
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @param int $id
     *
     */

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Relación: una reserva puede tener un viaje asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     *
     */
   public function viaje()
{
    return $this->hasOne(Viaje::class, 'id_reserva');
}

/**
 * Relación: una reserva puede tener un viaje activo asociado.
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasOne
 *
 */
    public function viajeActivo()
    {
        return $this->hasOne(Viaje::class, 'id_reserva')->whereNull('fecha_fin');
    }


    /**
     * Scope que filtra las reservas internas pertenecientes al árbol
     * de una dependencia determinada.
     *
     * Obtiene la dependencia padre junto con todas sus dependencias hijas
     * y construye un listado de IDs permitidos (padre + descendientes).
     * hija -> id_dependencia
     * hija -> hija
     * nieja -> hija
     *
     * Luego filtra las reservas donde:
     *  - La dependencia dueña del vehículo pertenezca al árbol.
     *  - La dependencia solicitante también pertenezca al mismo árbol.
     *
     * En caso de que la dependencia no exista, retorna una consulta vacía.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id_dependencia ID de la dependencia base.
     * @return \Illuminate\Database\Eloquent\Builder
     */

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



    /**
     * Scope que filtra las reservas externas respecto a una dependencia determinada.
     *
     * Se considera reserva externa cuando existe una relación entre dependencias
     * donde una pertenece al árbol (padre + hijas) de la dependencia indicada
     * y la otra no.
     *
     * Casos contemplados:
     *  - La dependencia (o alguna de sus hijas) es dueña del vehículo
     *    y la dependencia solicitante es externa al árbol.
     *
     *  - La dependencia (o alguna de sus hijas) es solicitante
     *    y la dependencia dueña es externa al árbol.
     *
     *  id_dependencia -> externa
     *  externa -> id_dependencia
     *  externa -> hija
     *  hija -> externa
     *  nieja -> externa
     *
     * Si la dependencia no existe, retorna una consulta vacía.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id_dependencia ID de la dependencia base.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeObtenerDependenciasExternas($query, $id_dependencia, $incluirAmbosSentidos = true)
    {
        $dependencia = Dependencia::with('dependenciasHijas')->find($id_dependencia);

        if (!$dependencia) {
            return $query->whereRaw('1 = 0');
        }

        $idsArbol = array_merge(
            [$dependencia->id],
            $dependencia->obtenerIdsHijas()
        );

        return $query->where(function ($q) use ($idsArbol, $incluirAmbosSentidos) {

            // Yo (o mis hijas) soy dueño y el solicitante es externo
            $q->where(function ($q1) use ($idsArbol) {
                $q1->whereIn('id_dependencia_duena', $idsArbol)
                    ->whereNotIn('id_dependencia_solicitante', $idsArbol);
            });

            // Solo si quiero ambos sentidos
            if ($incluirAmbosSentidos) {
                $q->orWhere(function ($q1) use ($idsArbol) {
                    $q1->whereIn('id_dependencia_solicitante', $idsArbol)
                    ->whereNotIn('id_dependencia_duena', $idsArbol);
                });
            }
        });
    }

    /**
     * Scope que filtra únicamente las reservas internas dentro de la
     * estructura jerárquica de dependencias.
     *
     * Se considera reserva interna cuando:
     *  - La dependencia dueña y la solicitante son la misma, o
     *  - Ambas pertenecen al mismo árbol jerárquico (misma dependencia
     *    padre y sus hijas).
     *
     * Para ello:
     *  - Se construye un mapa de todos los árboles de dependencias
     *    (cada dependencia junto con sus hijas).
     *  - Se filtran las reservas donde ambas dependencias
     *    (dueña y solicitante) pertenezcan al mismo conjunto de IDs.
     *
     * Esto garantiza que la reserva se realice dentro del mismo sector
     * organizacional y no corresponda a un préstamo externo.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
   public function scopeSoloInternas($query){

    $dependencias = \App\Models\Dependencia::all();

    $mapaArboles = [];

    foreach ($dependencias as $dep) {
        $mapaArboles[$dep->id] = array_merge([$dep->id], $dep->obtenerIdsHijas());
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
     * Scope que filtra únicamente las reservas externas puras.
     *
     * Se considera reserva externa cuando:
     *  - La dependencia dueña del vehículo es distinta a la dependencia solicitante.
     *  - No existe relación directa padre-hija entre ambas dependencias
     *    (ni la solicitante es hija directa de la dueña,
     *     ni la dueña es hija directa de la solicitante).
     *
     * Para ello:
     *  - Se compara que los IDs de dependencia sean diferentes.
     *  - Se utilizan subconsultas con whereNotExists para descartar relaciones jerárquicas directas entre ambas dependencias.
     *
     * Esto garantiza que la reserva corresponda a un préstamo real
     * entre sectores independientes dentro de la estructura organizacional.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
   public function scopeSoloExternas($query){
    return $query->whereColumn('reserva.id_dependencia_duena', '!=', 'reserva.id_dependencia_solicitante')

        // La solicitante NO es hija directa de la dueña
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('dependencia as d1')
              ->whereColumn('d1.id', 'reserva.id_dependencia_solicitante')
              ->whereColumn('d1.id_dependencia_padre', 'reserva.id_dependencia_duena');
        })

        // La dueña NO es hija directa de la solicitante
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('dependencia as d2')
              ->whereColumn('d2.id', 'reserva.id_dependencia_duena')
              ->whereColumn('d2.id_dependencia_padre', 'reserva.id_dependencia_solicitante');
        });
}


/**
 * Scope que filtra únicamente las reservas pendientes.
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @return \Illuminate\Database\Eloquent\Builder
 *
 *
 */
    public function scopePendientes($query){
        return $query->whereHas('estado_reserva', function ($q) {
            $q->where('estado', 'SOLICITADA');
        });
    }
}
