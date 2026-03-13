<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


/**
 * @class Direcciones
 *
 * Representa una categoría dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $calle Nombre de la calle
 * @property int $altura altura d ela calle fisica
 * @property string $ciudad nombre de la ciudad en la que se encuentra ubicada
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Direcciones extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table="direcciones";
    protected $fillable = [
        'calle',
        'altura',
        'ciudad',
    ];



      /**
     * Relación: una Direccion puede tener muchos vehiculos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehiculos() {
        return $this->hasMany(Vehiculo::class);
    }
  /**
     * Relación: una Direccion puede tener muchas dependencias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dependencia() {
        return $this->hasMany(Dependencia::class);
    }
  /**
     * Relación: una Direccion puede tener muchos viajes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function viaje() {
        return $this->hasMany(Viaje::class);
    }


    /**
     * Relacion : obtener localidad vinculada a la direccion
     */
    public static function obtenerLocalidades(){
        return Direcciones::distinct()->get('ciudad');
    }


}
