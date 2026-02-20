<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\EstadoReporte;
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


    // Cast del estado a enum (opcional pero recomendado)
    protected $casts = [
        'estado' => EstadoReporte::class,
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function comentarios()
    {
        return $this->hasMany(ReporteComentarios::class,'reporte_id');
    }

     public function isPendiente(): bool
    {
        return $this->estado === EstadoReporte::PENDIENTE;
    }

    public function isAtendido(): bool
    {
        return $this->estado === EstadoReporte::ATENDIDO;
    }

    public function isCerrado():bool{
        return $this->estado === EstadoReporte::CERRADO;
    }
}
