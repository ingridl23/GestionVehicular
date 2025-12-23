<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Viaje extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_vehiculo',
        'id_reserva',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'id_ultima_ubicacion'
    ];

    public function vehiculo(){
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function gasto(){
        return $this->hasOne(Gasto::class);
    }

    public function reserva(){
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

    public function ubicacion(){
        return $this->belongsTo(Direccion::class, 'id_ultima_ubicacion');
    }
}
