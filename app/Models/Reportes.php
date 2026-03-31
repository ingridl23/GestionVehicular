<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EstadoReporte;

/**
 * @class Reportes
 *
 * Representa una categoría dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $titulo nombre asignado al reporte generado
 * @property int $id_usuario identificador de vinculacion del usuario con el reporte generado
 * @property string|null $entidad_tipo  vinculacion con dependencia
 * @property int $entidad_id referencia de vinculacion con una dependencia
 * @property string $estado  estado del reporte generado
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */



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

    protected $table ="reporte";

    // Cast del estado a enum (opcional pero recomendado)
    protected $casts = [
        'estado' => EstadoReporte::class,
    ];
/**
 * Relacion: de Reportes puede venir de un usuario
 */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
/**
 * Reportes puede tener muchos comentarios
 */
    public function comentarios()
    {
        return $this->hasMany(ReporteComentarios::class,'reporte_id');
    }
/**
 * metodo de si esta pendiente me devuelve true o false para posteriormente usar esa validacion en un controller
 */
     public function isPendiente(): bool
    {
        return $this->estado === EstadoReporte::PENDIENTE;
    }
/**
 * metodo para saber si fue atendido el reporte y utilizar como validacion posteriormente en un controller
 */
    public function isAtendido(): bool
    {
        return $this->estado === EstadoReporte::ATENDIDO;
    }


/**
 *  metodo para saber si fue cerrado el reporte y utilizar como validacion posteriormente en un controller
 */
    public function isCerrado():bool{
        return $this->estado === EstadoReporte::CERRADO;
    }
}
