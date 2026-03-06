<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Enums;

use App\Api\Support\Traits\EnumTrait;

enum ManutencaoTipoEnum: string
{
    use EnumTrait;

    case PREVENTIVA = 'preventiva';
    case CORRETIVA = 'corretiva';
    case REVISAO = 'revisao';

    public function label(): string
    {
        return match ($this) {
            self::PREVENTIVA => 'Preventiva',
            self::CORRETIVA => 'Corretiva',
            self::REVISAO => 'Revisão',
        };
    }
}
