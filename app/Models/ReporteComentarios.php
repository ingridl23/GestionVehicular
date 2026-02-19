<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteComentarios extends Model
{
    protected $table = "reporte_comentarios";
    protected $fillable = [
        'reporte_id',
        'id_usuario',
        'comentario'
    ];



    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function reporte()
    {
        return $this->belongsTo(Reportes::class, 'reporte_id');
    }
}


