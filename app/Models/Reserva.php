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


    public function scopeObtenerDependenciasInternas($query, $id_dependencia){
        return $query->where('id_dependencia_duena', $id_dependencia) // Solo para reservas internas
            ->where(function ($q) use ($id_dependencia) {
                $q->where('id_dependencia_solicitante', $id_dependencia) // dependencia solicitante es la misma que la dependencia dueña
                ->orWhereHas('dependencia_solicitante', function ($q2) use ($id_dependencia) {
                    $q2->where('id_dependencia_padre', $id_dependencia); // La dependencia solicitante es hija de la dependencia dueña
                });
            });
    }

    public function scopeObtenerDependenciasExternas($query, $id_dependencia){
        // Mi dependencia o mis hijas participan
        return $query->where(function ($q) use ($id_dependencia) {
            $q->where('id_dependencia_duena', $id_dependencia)
            ->orWhere('id_dependencia_solicitante', $id_dependencia)
            ->orWhereHas('dependencia_duena', function ($q2) use ($id_dependencia) {
                $q2->where('id_dependencia_padre', $id_dependencia);
            })
            ->orWhereHas('dependencia_solicitante', function ($q2) use ($id_dependencia) {
                $q2->where('id_dependencia_padre', $id_dependencia);
            });
        })

        // No puede ser la misma dependencia en ambos campos
        ->whereColumn('id_dependencia_duena', '!=', 'id_dependencia_solicitante')

        // Excluir el caso: padre dueño → hija solicitante
        ->where(function ($q) use ($id_dependencia) {
            $q->where(function ($q1) use ($id_dependencia) {
                $q1->where('id_dependencia_duena', '!=', $id_dependencia);
            })
            ->orWhere(function ($q1) use ($id_dependencia) {
                $q1->whereDoesntHave('dependencia_solicitante', function ($q2) use ($id_dependencia) {
                    $q2->where('id_dependencia_padre', $id_dependencia);
                });
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
