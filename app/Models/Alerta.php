<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $fillable = [
        'tipo',
        'entidad_tipo',
        'entidad_id',
        'mensaje',
        'nivel',
        'activa',
        'fecha_generada',
        'fecha_resuelta'
    ];
}
