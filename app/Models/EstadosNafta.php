<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EstadosNafta extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'estado',
    ];

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }
}
