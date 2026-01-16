<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Vehiculo extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'vehiculo';


    protected $fillable = [
        'dominio',
        'marca',
        'modelo',
        'anio',
        'id_direccion_actual',
        'id_estado_vehiculo',
        'id_dependencia_duena',
        'id_estado_nafta',
        'control_satelital',
        'habilitado_prestamo',
        'condiciones_prestamo',
        'kilometros',
        'VTV',

    ];

    public function dependencia(){
        return $this->belongsTo(Dependencia::class, 'id_dependencia_duena');
    }

    public function nafta(){
        return $this->belongsTo(EstadosNafta::class, 'id_estado_nafta');
    }

    public function estado_vehiculo(){
        return $this->belongsTo(EstadosVehiculo::class, 'id_estado_vehiculo');
    }


    public function direccion() {
        return $this->belongsTo(Direcciones::class, 'id_direccion_actual');
    }

    public function reservas() {
        return $this->hasMany(Reserva::class,'id_vehiculo');
    }

    public function viajes(){
        return $this->hasMany(Viaje::class, 'id_vehiculo');
    }
}
