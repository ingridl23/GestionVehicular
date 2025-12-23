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
        'id_dependencia',
        'id_direccion',
    ];

    // Dependencia padre
    public function dependenciaPadre() {
        return $this->belongsTo(Dependencia::class, 'id_dependencia');
    }

    // Dependencias hijas
    public function dependenciasHijas() {
        return $this->hasMany(Dependencia::class, 'id_dependencia');
    }

    public function direccion() {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }

}
