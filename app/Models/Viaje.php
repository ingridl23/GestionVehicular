<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
/**
 * @class Viaje
 *
 *
 * Representa un viaje dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property int $id_vehiculo
 * @property int $id_reserva
 * @property date $fecha_inicio
 * @property date $fecha_fin
 * @property int $kilometros_inicio
 * @property int $kilometros_fin
 * @property int $id_estado_nafta_inicio
 * @property int $id_estado_nafta_fin
 * @property string $observaciones
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Viaje extends Model
{
   use HasFactory, Notifiable;

    protected $table = 'viaje';

    protected $fillable = [
        'id_vehiculo',
        'id_reserva',
        'fecha_inicio',
        'fecha_fin',
        'kilometros_inicio',
        'kilometros_fin',
        'id_estado_nafta_inicio',
        'id_estado_nafta_fin',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];


    public function vehiculo(){
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function gasto(){
        return $this->hasOne(Gasto::class, 'id_viaje');
    }

    public function reserva(){
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

   public function estadoNaftaInicio(){
        return $this->belongsTo(EstadosNafta::class, 'id_estado_nafta_inicio');
    }

    public function estadoNaftaFin(){
        return $this->belongsTo(EstadosNafta::class, 'id_estado_nafta_fin');
    }

   public function coordenadas()
{
    return $this->hasMany(CoordenadasVehiculo::class, 'id_viaje');
}

public function ultimaCoordenada()
{
    return $this->hasOne(CoordenadasVehiculo::class, 'id_viaje')->latest('fecha_hora');
}


public function getEstadoViajeAttribute(): string
{
    if ($this->fecha_fin) {
        return 'FINALIZADO';
    }
    // Leer el estado de la reserva asociada
    return $this->reserva?->estado_reserva?->estado ?? 'EN_CURSO';
}
}
