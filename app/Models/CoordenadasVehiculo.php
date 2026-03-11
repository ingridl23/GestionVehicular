<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @class Coordenadas Viaje
 *
 *
 * Representa la ubicacion real del vehiculo durante un viaje dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property Decimal $coordenada_inicial
 * @property Decimal $coordenada_final
 * @property float $precision
 * @property int $id_vehiculo
 * @property Date $fecha_hora

 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CoordenadasVehiculo extends Model
{
     use HasFactory, Notifiable;

    protected $table = 'coordenadas_vehiculo';

   protected $fillable = [
        'latitud',
        'longitud',
        'precision',
        'id_vehiculo',
        'id_viaje',
        'fecha_hora'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime'
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class,'id_viaje');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class,'id_vehiculo');
    }


}
