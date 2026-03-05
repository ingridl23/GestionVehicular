<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EstadosViaje extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'estados_viaje';

    protected $fillable = [
        'estado',

    ];

public function viajes() {
    return $this->hasMany(Viaje::class, 'id_estado_viaje');
}


}
