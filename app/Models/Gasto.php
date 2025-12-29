<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Gasto extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'kilometros',
        'id_estados_nafta',
        'id_viaje',
        'monto'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_viaje');
    }
    public function viajes()
    {
        return $this->belongsTo(Viaje::class, 'id_viaje');
    }

    public function estadoNafta()
    {
        return $this->belongsTo(Estados_nafta::class, 'id_estados_nafta');
    }
}
