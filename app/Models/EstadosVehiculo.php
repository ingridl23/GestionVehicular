<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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
