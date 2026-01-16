<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Direcciones extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    
    protected $fillable = [
        'calle',
        'altura',
        'ciudad',
    ];

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }

    public function dependencia() {
        return $this->hasMany(Dependencia::class);
    }

    public function viaje() {
        return $this->hasMany(Viaje::class);
    }

    public static function obtenerLocalidades(){
        return Direcciones::distinct()->get('ciudad');
    }


}
