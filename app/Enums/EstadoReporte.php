<?php

namespace App\Enums;

enum EstadoReporte: string
{
   case PENDIENTE = 'pendiente';
    case EN_REVISION = 'en_revision';
    case ATENDIDO = 'atendido';
    case CERRADO = 'cerrado';

    /**
     * Obtener todos los valores
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtener label para mostrar en UI
     */
    public function label(): string
    {
        return match($this) {
            self::PENDIENTE => 'Pendiente',
            self::EN_REVISION => 'En Revisión',
            self::ATENDIDO => 'Atendido',
            self::CERRADO => 'Cerrado',
        };
    }

    /**
     * Obtener color para badges
     */
    public function color(): string
    {
        return match($this) {
            self::PENDIENTE => 'yellow',
            self::EN_REVISION => 'blue',
            self::ATENDIDO => 'green',
            self::CERRADO => 'gray',
        };
    }
}
