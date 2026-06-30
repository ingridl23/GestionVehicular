<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @class EstadosViaje
 *
 * Representa un carnet  de conducir dentro del sistema en relacion a un usuario existente.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $estado Nombre de la categoria
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 *
 */
class EstadosViaje extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'estados_viaje';

    protected $fillable = [
        'estado',

    ];


    /**
     * Relacion: estado con viajes
     */
public function viajes() {
    return $this->hasMany(Viaje::class, 'id_estado_viaje');
}


}
