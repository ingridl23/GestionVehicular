<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Dependencia extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'activa',
        'id_dependencia_padre',
        'id_direccion',
    ];

    // Dependencia padre
    public function dependenciaPadre() {
        return $this->belongsTo(Dependencia::class, 'id_dependencia_padre');
    }

    // Dependencias hijas
    public function dependenciasHijas() {
        return $this->hasMany(Dependencia::class, 'id_dependencia_padre')->with('dependenciasHijas');
    }



    public function direccion() {
        return $this->belongsTo(Direcciones::class, 'id_direccion');
    }

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class, 'id_dependencia_duena');
    }


    public function obtenerIdsHijas(): array
    {
        $ids = [];

        foreach ($this->dependenciasHijas as $hija) {
            $ids[] = $hija->id;
            $ids = array_merge($ids, $hija->obtenerIdsHijas());
        }

        return $ids;
    }

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

    public static function obtenerTodosLosPadres(){
        //Has: Se fija qie exista la relacion (una dependencia tenga al menos una hija)
        return Dependencia::has('dependenciasHijas')->get();
    }

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
