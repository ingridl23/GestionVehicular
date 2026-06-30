<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @class EstadosVehiculo
 *
 * Representa un carnet  de conducir dentro del sistema en relacion a un usuario existente.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $estado Nombre de la categoria
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 */
class EstadosVehiculo extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'estados_vehiculos';

    protected $fillable = [
        'estado',

    ];
 /**
     * Relación: una estado lo pueden tener muchos vehiculos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }


}
