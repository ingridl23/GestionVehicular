<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @class Dependencia
 *
 * Representa una Dependencia dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $categoria Nombre de la dependencia
 * @property booolean $activa valor booleano para saber si la dependencia esta vigente en el sistema y en la vida diaria
 * @property int $id_dependencia_padre identificador de referencia del origen del a oficina.
 * @property int $id_direccion  referencia de ubicacion fisica de la dependencia.
 * @property \Carbon\Carbon $created_at Fecha de creación
 * @property \Carbon\Carbon $updated_at Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Dependencia extends Model
{
    use HasFactory, Notifiable;
     protected $table ='dependencia';

    protected $fillable = [
        'nombre',
        'activa',
        'id_dependencia_padre',
        'id_direccion',
    ];

     /**
     * Relación: una dependencia puede tener una dependencia de la que desciende.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // Dependencia padre
    public function dependenciaPadre() {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_padre');
    }
 /**
     * Relación: una dependencia puede tener muchas subdependencias hijas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // Dependencias hijas
    public function dependenciasHijas() {
        return $this->hasMany(Dependencia::class, 'id_dependencia_padre')->with('dependenciasHijas');
    }


    /**
     * Relación: una Dependencia puede tener una direccion.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function direccion() {
        return $this->belongsTo(Direcciones::class, 'id_direccion');
    }

    /**
     * Relación:una Dependencia puede tener mucos vehiculos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehiculos() {
        return $this->hasMany(Vehiculo::class, 'id_dependencia_duena');
    }


    /**
     * Metodo para obtner del arbol jerarquico los id de dependencias hijas
     * @return array
     *
     */
    public function obtenerIdsHijas(): array
    {
        $ids = [];

        foreach ($this->dependenciasHijas as $hija) {
            $ids[] = $hija->id;
            $ids = array_merge($ids, $hija->obtenerIdsHijas());
        }

        return $ids;
    }


    /**
     * Scope de informacion para dependencias internas / hijas
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function scopeObtenerDependenciasInternas($query, $id_dependencia){

        $dependencia = Dependencia::find($id_dependencia);

        if (!$dependencia) {
            return $query->whereRaw('1 = 0');
        }

        // Se obtienen todsos los ID`s (el de la dependencia padre + todos sus hijos)
        $idsPermitidos = array_merge(
            [$dependencia->id],
            $dependencia->obtenerIdsHijas()
        );

        return $query->where(function ($q) use ($idsPermitidos) {
            $q->whereIn('id', $idsPermitidos)
            ->orWhereIn('id_dependencia_padre', $idsPermitidos);
        });
    }

    /**
     * Metodo para obtener las dependencias padres/bases del arbol de jerarquia de oficinas
     * @return array
     *
     */

    public static function obtenerTodosLosPadres(){
        //Has: Se fija qie exista la relacion (una dependencia tenga al menos una hija)
        return Dependencia::has('dependenciasHijas')->get();
    }


    /**
     * Metodo que permite filtrar dependencias que pueden ser desactivas y asi validar su lugar en el arbol jerarquico y en el uso del sistema.
    * @return ?string

    */
    public function puedeSerDesactivada() : ?string{
        // Hijas activas
        if ($this->dependenciasHijas()
            ->where('activa', true)
            ->exists()) {
            return 'dependencias hijas activas.';
        }

        // Reservas activas como dueña
        if (Reserva::where('id_dependencia_duena', $this->id)
            ->whereHas('estado_reserva', function ($q) {
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA', 'EN CURSO']);
            })
            ->exists()) {

            return 'reservas activas como dependencia dueña.';
        }

        // Reservas activas como solicitante
        if (Reserva::where('id_dependencia_solicitante', $this->id)
            ->whereHas('estado_reserva', function ($q) {
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA', 'EN CURSO']);
            })
            ->exists()) {

            return 'reservas activas como dependencia solicitante.';
        }

        // Vehículos asociados
        if ($this->vehiculos()->exists()) {
            return 'vehículos asociados.';
        }

        // Usuarios asociados
        if (User::where('id_dependencia', $this->id)->exists()) {
            return 'usuarios asociados.';
        }

        return null;
    }




}
