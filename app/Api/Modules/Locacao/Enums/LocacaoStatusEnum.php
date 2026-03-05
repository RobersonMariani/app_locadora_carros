<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Enums;

use App\Api\Support\Traits\EnumTrait;

enum LocacaoStatusEnum: string
{
    use EnumTrait;

    case RESERVADA = 'reservada';
    case ATIVA = 'ativa';
    case FINALIZADA = 'finalizada';
    case CANCELADA = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::RESERVADA => 'Reservada',
            self::ATIVA => 'Ativa',
            self::FINALIZADA => 'Finalizada',
            self::CANCELADA => 'Cancelada',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::RESERVADA => $target->equals(self::ATIVA, self::CANCELADA),
            self::ATIVA => $target->equals(self::FINALIZADA, self::CANCELADA),
            self::FINALIZADA, self::CANCELADA => false,
        };
    }
}
