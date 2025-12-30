<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EstadosVehiculo extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'estado',
    ];

    public const DISPONIBLE = 1;
    public const EN_USO     = 2;
    public const BAJA       = 3;
    public const EN_MANTENIMIENTO = 4;
    public const NO_DISPONIBLE = 5 ;
    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }


}
