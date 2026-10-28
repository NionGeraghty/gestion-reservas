<?php
declare(strict_types=1);
namespace App\Modelo;

enum EstadoReserva: string
{
    case Pendiente = 'pendiente';
    case Confirmada = 'confirmada';
    case Cancelada = 'cancelada';

    public function puedeCambiar(EstadoReserva $nuevoEstado): bool
    {
        return match($this)
        {
            self::Pendiente => $nuevoEstado!==self::Pendiente,
            self::Confirmada => $nuevoEstado===self::Cancelada,
            self::Cancelada => false,
        };
    }
}