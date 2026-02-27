<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadosReserva extends Model
{
    protected $table = 'estados_reservas';
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

     /**
     * Relación: una estado lo pueden tener muchas reservas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
