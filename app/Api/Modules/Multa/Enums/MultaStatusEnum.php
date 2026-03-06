<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Enums;

use App\Api\Support\Traits\EnumTrait;

enum MultaStatusEnum: string
{
    use EnumTrait;

    case PENDENTE = 'pendente';
    case PAGA = 'paga';
    case CONTESTADA = 'contestada';
    case CANCELADA = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::PAGA => 'Paga',
            self::CONTESTADA => 'Contestada',
            self::CANCELADA => 'Cancelada',
        };
    }
}
