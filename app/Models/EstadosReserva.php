<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadosReserva extends Model
{
    use hasFactory, Notifiable;

    protected $fillable = [
        'estado',
    ];

    //para acordarse
    public const EN_CURSO = 1;
    public const FINALIZADA = 2;
    public const PENDIENTE = 3;
    public const CANCELADA = 4;
    public const RECHAZADA = 5;
    public const APROBADA = 6;

    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
