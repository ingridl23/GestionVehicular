<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reserva extends Model
{
    use HasFactory, Notifiable;

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


    public function vehiculo() {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function estado_reserva() {
        return $this->belongsTo(EstadosReserva::class, 'id_estado_reserva');
    }

    public function dependencia_duena() {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_duena');
    }

    public function dependencia_solicitante() {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_solicitante');
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function viaje(){
        return $this->hasMany(Viaje::class);
    }

    // Obtiene:
    //Casos que involucran la dependencia del usuario con dependencias internas
    // independencias hijas involucradas
    // id_dependencia -> hija
    // hija -> id_dependencia
    // hija -> hija
    // nieja -> hija

    public function scopeObtenerDependenciasInternas($query, $id_dependencia){
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

    public function scopeObtenerDependenciasExternas($query, $id_dependencia){
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

    public function scopeSoloInternas($query){
        return $query->where(function ($q) {

            // Misma dependencia (dueña = solicitante)
            $q->whereColumn(
                'id_dependencia_duena',
                'id_dependencia_solicitante'
            )

            // Solicitante es hija de la dueña
            ->orWhereHas('dependencia_solicitante', function ($q2) {
                $q2->whereColumn(
                    'dependencias.id_dependencia_padre',
                    'reservas.id_dependencia_duena'
                );
            })

            // Dueña es hija de la solicitante
            ->orWhereHas('dependencia_duena', function ($q3) {
                $q3->whereColumn(
                    'dependencias.id_dependencia_padre',
                    'reservas.id_dependencia_solicitante'
                );
            });

        });
    }

    public function scopeSoloExternas($query){
        return $query->where(function ($q) {

            // Misma dependencia (dueña = solicitante)
            $q->whereColumn(
                'id_dependencia_duena', '!=',
                'id_dependencia_solicitante'
            )

            // Solicitante no es hija de la dueña
            ->whereDoesntHave('dependencia_solicitante', function ($q2) {
                $q2->whereColumn(
                    'dependencias.id_dependencia_padre',
                    'reservas.id_dependencia_duena'
                );
            })

            // Dueña no es hija de la solicitante
            ->whereDoesntHave('dependencia_duena', function ($q3) {
                $q3->whereColumn(
                    'dependencias.id_dependencia_padre',
                    'reservas.id_dependencia_solicitante'
                );
            });

        });
    }
}
