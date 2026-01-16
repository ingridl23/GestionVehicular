<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Dependencia extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'activa',
        'id_dependencia_padre',
        'id_direccion',
    ];

    // Dependencia padre
    public function dependenciaPadre() {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_padre');
    }

    // Dependencias hijas
    public function dependenciasHijas() {
        return $this->hasMany(Dependencia::class, 'id_dependencia_padre');
    }

    public function direccion() {
        return $this->belongsTo(Direcciones::class, 'id_direccion');
    }

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }

}
