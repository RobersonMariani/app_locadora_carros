<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Enums;

use App\Api\Support\Traits\EnumTrait;

enum VistoriaTipoEnum: string
{
    use EnumTrait;

    case RETIRADA = 'retirada';
    case DEVOLUCAO = 'devolucao';

    public function label(): string
    {
        return match ($this) {
            self::RETIRADA => 'Retirada',
            self::DEVOLUCAO => 'Devolução',
        };
    }
}
