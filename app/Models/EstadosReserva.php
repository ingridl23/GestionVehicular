<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estados_reserva extends Model
{
    use hasFactory, Notifiable;

    protected $fillable = [
        'estado',
    ];


    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
