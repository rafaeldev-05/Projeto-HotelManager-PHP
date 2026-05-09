<?php

declare(strict_types=1);

namespace App\Models;

final class HotelStatus
{
    public const ROOM_AVAILABLE = 'Disponivel';
    public const ROOM_RESERVED = 'Reservado';
    public const ROOM_OCCUPIED = 'Ocupado';
    public const ROOM_CLEANING = 'Em limpeza';
    public const ROOM_MAINTENANCE = 'Em manutencao';

    public const RESERVATION_PENDING = 'Pendente de confirmacao';
    public const RESERVATION_CONFIRMED = 'Confirmada';
    public const RESERVATION_STAYING = 'Em hospedagem';
    public const RESERVATION_FINISHED = 'Finalizada';
    public const RESERVATION_CANCELLED = 'Cancelada';
    public const RESERVATION_NO_SHOW = 'Nao compareceu';

    public static function roomStatuses(): array
    {
        return [
            self::ROOM_AVAILABLE,
            self::ROOM_RESERVED,
            self::ROOM_OCCUPIED,
            self::ROOM_CLEANING,
            self::ROOM_MAINTENANCE,
        ];
    }
}

