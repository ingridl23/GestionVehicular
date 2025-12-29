<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vehiculo extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_dependencia_duena',
        'dominio',
        'marca',
        'modelo',
        'anio',
        'id_estado_vehiculo',
        'id_direccion_actual',
        'prestamo',
        'condiciones_prestamo',
        'kilometros',
        'id_estado_nafta',
        'VTV'
    ];

    public function dependencia(){
        return $this->belongsTo(Dependencia::class, 'id_dependencia_duena');
    }

    public function nafta(){
        return $this->belongsTo(Estados_nafta::class, 'id_estado_nafta');
    }

    public function estado_vehiculo(){
        return $this->belongsTo(Estados_vehiculo::class, 'id_estado_vehiculo');
    }

    public function direccion() {
        return $this->belongsTo(Direccion::class, 'id_direccion_actual');
    }

    public function reservas() {
        return $this->hasMany(Reserva::class,'id_vehiculo');
    }

    public function viajes(){
        return $this->hasMany(Viaje::class, 'id_vehiculo');
    }
}
