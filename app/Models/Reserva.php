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

    public function vehiculo() {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function estado_reserva() {
        return $this->belongsTo(Estados_reserva::class, 'id_estado_reserva');
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
}
