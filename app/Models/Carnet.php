<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;


/**
 * @class Carnet
 *
 * Representa un carnet  de conducir dentro del sistema en relacion a un usuario existente.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property \Carbon\Carbon $fecha_emision Fecha de emision del carnet del individuo
 * @property \Carbon\Carbon $fecha_vencimiento Fecha de caducidad del carnet del individuo
 * @property boolean $vigente  Asegura el estado del carnet cargado dentro del sistema
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Carnet extends Model
{
    use hasFactory, Notifiable;

    protected $table ='carnet';
    protected $fillable = [
        'fecha_vencimiento',
        'fecha_emision',
        'id_usuario',
        'vigente'
    ];
    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_emision' => 'date',
        'vigente' => 'boolean',
    ];


    /**
     * Relación: un carnet pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function user() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

   /**
    * Permite verificar si el carnet vinculado al usuario esta en fecha para cuando el usuario realice una reserva de un vehiculo
    * se compruebe que esta en condiciones optimas de conducir reglamentariamente.
    *
    * @return boolean
    *
    */

    public static function carnetVigente($id){
       $carnet = Carnet::where('id_usuario', $id)
    ->orderByDesc('fecha_vencimiento')
    ->first();
        if (!$carnet || !$carnet->id_usuario) {
            return false;
        }

        $fecha_hoy = Date::now();
        return $fecha_hoy->lessThanOrEqualTo($carnet->fecha_vencimiento);
    }

}
