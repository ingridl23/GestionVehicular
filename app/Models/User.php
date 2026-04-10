<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'user';
    protected $fillable = [
        'name',
        'lastname',
        'legajo',
        'email',
        'password',
        'id_dependencia',
        'id_photo_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_dependencia');
    }

    public function carnet()
    {
        return $this->hasOne(Carnet::class, 'id_usuario');
    }


    public function reservas() {
        return $this->hasMany(Reserva::class, 'id_usuario');
    }

  public function imagenProfile()
{
    return $this->belongsTo(ImagenProfile::class, 'id_photo_profile');
}

    public function puedeConducir(): bool
    {
        return $this->carnet && $this->carnet->vigente;
    }

public function licenciaVigente(): bool
{
    if (!$this->fecha_vencimiento_licencia) {
        return false;
    }

    return now()->lessThanOrEqualTo($this->fecha_vencimiento_licencia);
}


/**
 * Metodo para consultar carnet proximos a vencer, util para auditoria y funcionalidad de alertas automaticas dentro del sistema
 */
public function carnetPorVencer(int $dias = 30): bool
{
    if (!$this->carnet) {
        return false;
    }

    return now()->diffInDays($this->carnet->fecha_vencimiento, false) <= $dias
        && now()->lessThanOrEqualTo($this->carnet->fecha_vencimiento);
}

/**
 * Metodo para obtener si una reserva esta activa dada la fehca actual dentro del sistema
 */
public function getViajeActivoAttribute()
{
    return \App\Models\Viaje::whereHas('reserva', function ($q) {
            $q->where('id_usuario', $this->id);
        })
        ->whereNull('fecha_fin')
        ->first();
}


}
