<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
 * @class ReporteComentarios
 *
 * Representa una categoría dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property int $reporte_id identificador del reporte al que pertenece
 * @property int $id_usuario identificador de vinculacion delusuario ocn el eporte generado
 * @property string|null $comentario  espacio de informacion generado para el reporte
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ReporteComentarios extends Model
{


    protected $table = "reporte_comentarios";
    protected $fillable = [
        'reporte_id',
        'id_usuario',
        'comentario'
    ];


/**
 * Relacion: de ReporteComentario puede venir de un usuario
 */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }



/**
 * Relacion: de ReporteComentario puede venir de un reporte
 */
    public function reporte()
    {
        return $this->belongsTo(Reportes::class, 'reporte_id');
    }
}


