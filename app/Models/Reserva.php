<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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
     * Para resolver la jerarquía completa de dependencias se utiliza un
     * CTE recursivo (WITH RECURSIVE), permitiendo recorrer todos los niveles
     * de relaciones padre–hijo sin límite de profundidad.
     *
     * Requisitos:
     * - Base de datos compatible con CTE recursivos (MySQL 8+, PostgreSQL).
     * - No compatible con MySQL 5.7 ni SQLite.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeSoloInternas($query){

        return $query->where(function ($q) {

            $q->whereColumn( 'id_dependencia_duena', 'id_dependencia_solicitante')

                // Solicitante pertenece al árbol completo de la dueña
                ->orWhereRaw("
            reservas.id_dependencia_solicitante IN (
                WITH RECURSIVE dependencias_arbol AS (
                    SELECT id
                    FROM dependencias
                    WHERE id = reservas.id_dependencia_duena

                    UNION ALL

                    SELECT d.id
                    FROM dependencias d
                    INNER JOIN dependencias_arbol da
                        ON d.id_dependencia_padre = da.id
                )
                SELECT id FROM dependencias_arbol
            )
        ")

            // Dueña pertenece al árbol completo de la solicitante
                ->orWhereRaw("
            reservas.id_dependencia_duena IN (
                WITH RECURSIVE dependencias_arbol AS (
                    SELECT id
                    FROM dependencias
                    WHERE id = reservas.id_dependencia_solicitante

                    UNION ALL

                    SELECT d.id
                    FROM dependencias d
                    INNER JOIN dependencias_arbol da
                        ON d.id_dependencia_padre = da.id
                )
                SELECT id FROM dependencias_arbol
            )
        ");
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
 * - La dependencia solicitante NO pertenece al árbol completo de la dueña.
 * - La dependencia dueña NO pertenece al árbol completo de la solicitante.
 *
 * Para evaluar correctamente la jerarquía completa de dependencias se utilizan
 * CTEs recursivos (WITH RECURSIVE), permitiendo excluir cualquier relación
 * padre–hijo en todos los niveles.
 *
 *  Requisitos:
 * - Base de datos compatible con CTE recursivos (MySQL 8+, PostgreSQL).
 * - No compatible con MySQL 5.7 ni SQLite.
 *
 * @param \Illuminate\Database\Eloquent\Builder $query
 * @return \Illuminate\Database\Eloquent\Builder
 */
    public function scopeSoloExternas($query){
        return $query->where(function ($q) {

            // Dependencias distintas
            $q->whereColumn(
                'id_dependencia_duena',
                '!=',
                'id_dependencia_solicitante'
            )

            // La solicitante NO pertenece al árbol completo de la dueña
            ->whereRaw("
                reservas.id_dependencia_solicitante NOT IN (
                    WITH RECURSIVE dependencias_arbol AS (
                        SELECT id
                        FROM dependencias
                        WHERE id = reservas.id_dependencia_duena

                        UNION ALL

                        SELECT d.id
                        FROM dependencias d
                        INNER JOIN dependencias_arbol da
                            ON d.id_dependencia_padre = da.id
                    )
                    SELECT id FROM dependencias_arbol
                )
            ")

            // La dueña NO pertenece al árbol completo de la solicitante
            ->whereRaw("
                reservas.id_dependencia_duena NOT IN (
                    WITH RECURSIVE dependencias_arbol AS (
                        SELECT id
                        FROM dependencias
                        WHERE id = reservas.id_dependencia_solicitante

                        UNION ALL

                        SELECT d.id
                        FROM dependencias d
                        INNER JOIN dependencias_arbol da
                            ON d.id_dependencia_padre = da.id
                    )
                    SELECT id FROM dependencias_arbol
                )
            ");
        });
    }
}
