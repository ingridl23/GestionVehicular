<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\EstadosNafta;

/**
 * @class Gasto
 *
 * Representa el gasto en combustible y mantenimineto de vehiculos dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property int $kilometros distanica recorrida en un viaje por un vehiculo
 * @property int $id_estados_nafta id de referencia al actualizar el estado del combustible de un vehiculo
 * @property int $id_viaje id de referencia para la vinculacion de un gasto con un viaje realizado
 * @property double $monto referencia numerica del valor del gato obtenido
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Gasto extends Model
{
    use HasFactory, Notifiable;


    protected $table = 'gasto';
    protected $fillable = [
        'kilometros',
        'id_estado_nafta',
        'id_viaje',
        'monto',
        'litros_consumidos',
        'precio_litro',
        'fecha_calculo'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function viajes()
    {
        return $this->belongsTo(Viaje::class, 'id_viaje');
    }

    public function estadoNafta()
    {
        return $this->belongsTo(EstadosNafta::class, 'id_estado_nafta');
    }

}
