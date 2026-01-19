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
    public const SOLICITADA = 1;
    public const APROBADA = 2;
    public const EN_CURSO = 3;
    public const FINALIZADA = 4;
    public const CANCELADA = 5;
    public const RECHAZADA = 6;

    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
