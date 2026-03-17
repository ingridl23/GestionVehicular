<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;
/**
 * @class Vehiculo
 *
 *
 * Representa una vehiculo dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $dominio
 * @property string $marca
 * @property int $anio
 * @property  int $id_direccion_actual
 * @property int $id_estado_vehiculo
 * @property int $id_dependencia_duena
 * @property int $id_estado_nafta
 * @property boolean $control_satelital
 * @property boolean $habilitado_prestamo
 * @property string $condiciones_prestamo
 * @property  int $kilometros
 * @property date $vtv
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Vehiculo extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'vehiculos';


    protected $fillable = [
        'dominio',
        'marca',
        'modelo',
        'anio',
        'id_direccion_actual',
        'id_estado_vehiculo',
        'id_dependencia_duena',
        'id_estado_nafta',
        'control_satelital',
        'gestya_device_id',
        'habilitado_prestamo',
        'condiciones_prestamo',
        'kilometros',
        'vtv',

    ];
    protected $casts = [
    'control_satelital' => 'boolean',
    'habilitado_prestamo' => 'boolean',
    'vtv' => 'date',
];

    public function dependenciaDuena(){
        return $this->belongsTo(Dependencia::class, 'id_dependencia_duena');
    }

    public function estadoNafta(){
        return $this->belongsTo(EstadosNafta::class, 'id_estado_nafta');
    }

    public function estadoVehiculo(){
        return $this->belongsTo(EstadosVehiculo::class, 'id_estado_vehiculo');
    }


    public function direccionActual() {
        return $this->belongsTo(Direcciones::class, 'id_direccion_actual');
    }

    public function reservas() {
        return $this->hasMany(Reserva::class,'id_vehiculo');
    }

    public function viajes(){
        return $this->hasMany(Viaje::class, 'id_vehiculo');
    }


    /**
     * Metodo estatico de control de vtv vigentes en relacion a vehiculos registrados en el sistema. Permite utilizar este dato para
     * generar alertas automaticas dentor del sistema y tomar accion sobre los mismos dependiendo del estado de cada vehiculo.
     */
    public static function vtv_vigente($id){
        $vehiculo = Vehiculo::find($id);

        if (!$vehiculo || !$vehiculo->vtv) {
            return false;
        }

        $fecha_hoy = Date::now();
        return $fecha_hoy->lessThanOrEqualTo($vehiculo->vtv);
    }
}
