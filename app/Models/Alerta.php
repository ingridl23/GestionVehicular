<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
/**
 * @class Alerta
 *
 * Representa una categoría dentro del sistema.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $tipo Nombre de la categoria
 * @property string $entidad_tipo Descripción de la categoría
 * @property string $entidad_id
 * @property string $nivel  describe que nivel de alerta pertenece
 * @property boolean $activa describe el estado
 * @property \Carbon\Carbon $fecha_generada Fecha de creación
 * @property \Carbon\Carbon $fecha_resuelta Fecha de última actualización
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Alerta extends Model
{
      /** @var string $table Nombre de la tabla asociada */
    protected $table = "alertas";
    protected $fillable = [
        'tipo',
        'entidad_tipo',
        'entidad_id',
        'mensaje',
        'nivel',
        'activa',
        'fecha_generada',
        'fecha_resuelta'
    ];


    protected $casts = [
        'activa' => 'boolean',
        'fecha_generada' => 'datetime',
        'fecha_resuelta' => 'datetime',
    ];

    /**
     * Relación polimórfica con la entidad
     * (puede ser vehiculo, reserva, usuario, dependencia, etc.)
     */
    public function entidad(): MorphTo
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }

    /**
     * Relación específica con vehículo
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vehiculo::class, 'entidad_id')
            ->where('entidad_tipo', 'vehiculo');
    }

    /**
     * Relación específica con reserva
     */
    public function reserva(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Reserva::class, 'entidad_id')
            ->where('entidad_tipo', 'reserva');
    }

    /**
     * Relación específica con usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'entidad_id')
            ->where('entidad_tipo', 'usuario');
    }

    /**
     * Relación específica con dependencia
     */
    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dependencia::class, 'entidad_id')
            ->where('entidad_tipo', 'dependencia');
    }

    /**
     * Scope para alertas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para alertas por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para alertas por entidad
     */
    public function scopePorEntidad($query, string $tipo, int $id)
    {
        return $query->where('entidad_tipo', $tipo)
                    ->where('entidad_id', $id);
    }

    /**
     * Obtener ícono según tipo de alerta
     */
    public function getIconoAttribute(): string
    {
        return match($this->tipo) {
            'vtv_vencida', 'vtv_por_vencer' => 'fa-id-card',
            'vehiculo_fuera_servicio' => 'fa-car-crash',
            'reserva_rechazada', 'reserva_vencida', 'reserva_por_vencer' => 'fa-calendar-times',
            'mantenimiento_pendiente' => 'fa-wrench',
            'combustible_bajo' => 'fa-gas-pump',
            'licencia_vencimiento' => 'fa-id-badge',
            default => 'fa-bell'
            };
            }

            /**
             * Obtener color según tipo de alerta
            */






}
