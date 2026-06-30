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
    protected $table = "alerta";
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     *
     */

    public function entidad(): MorphTo
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }

    /**
     * Relación específica con vehículo
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Vehiculo::class, 'entidad_id')
            ->where('entidad_tipo', 'vehiculo');
    }

    /**
     * Relación específica con reserva
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     *
     */

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Reserva::class, 'entidad_id')
            ->where('entidad_tipo', 'reserva');
    }

    /**
     * Relación específica con usuario
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'entidad_id');

    }

    /**
     * Relación específica con dependencia
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dependencia::class, 'entidad_id')
            ->where('entidad_tipo', 'dependencia');
    }

    /**
     * Scope para alertas activas
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para alertas por tipo
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para alertas por entidad
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     *
     */
    public function scopePorEntidad($query, string $tipo, int $id)
    {
        return $query->where('entidad_tipo', $tipo)
                    ->where('entidad_id', $id);
    }

    /**
     * Obtener ícono según tipo de alerta
     * @return string
     *
     *
     */
    public function getIconoAttribute(): string
    {
        return match($this->tipo) {
            'vtv_vencida', 'vtv_por_vencer' => 'fa-id-card',
             'vehiculo_fuera_servicio' => 'bg-red-100 text-red-600',
            'reserva_rechazada', 'reserva_vencida', 'reserva_por_vencer' => 'fa-calendar-times',
            'mantenimiento_pendiente' => 'bg-blue-100 text-blue-600',
            'combustible_bajo' => 'fa-gas-pump',
              'licencia_vencimiento' => 'bg-yellow-100 text-yellow-600',
           default => 'bg-gray-100 text-gray-600',
            };
            }

            /**
             * Obtener color según tipo de alerta
            */






}
