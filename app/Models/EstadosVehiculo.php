<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EstadosVehiculo extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'estados_vehiculo';

    protected $fillable = [
        'estado',

    ];

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }


}
