<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * @class PrecioCombustible
 *
 * Representa una categoría dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 *
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class PrecioCombustible extends Model
{
protected $table = 'precios_combustible';

protected $fillable = [
'precio_litro',
'fecha'
];

}
