<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Enums;

use App\Api\Support\Traits\EnumTrait;

enum ManutencaoStatusEnum: string
{
    use EnumTrait;

    case AGENDADA = 'agendada';
    case EM_ANDAMENTO = 'em_andamento';
    case CONCLUIDA = 'concluida';
    case CANCELADA = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::AGENDADA => 'Agendada',
            self::EM_ANDAMENTO => 'Em Andamento',
            self::CONCLUIDA => 'Concluída',
            self::CANCELADA => 'Cancelada',
        };
    }
}
