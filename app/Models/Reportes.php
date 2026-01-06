<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reportes extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'id_usuario',
        'entidad_tipo',
        'entidad_id',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
