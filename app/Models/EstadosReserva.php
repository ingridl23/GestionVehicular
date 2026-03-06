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
   public const SOLICITADA = 1;
   public const APROBADA   = 2;  // ← era 6, es 2
   public const EN_CURSO   = 3;  // ← era 1, es 3
   public const FINALIZADA = 4;  // ← era 2, es 4
   public const CANCELADA  = 5;  // ← era 4, es 5
   public const RECHAZADA  = 6;  // ← era 5, es 6


     /**
     * Relación: una estado lo pueden tener muchas reservas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
