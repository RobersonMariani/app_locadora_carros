<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Enums;

use App\Api\Support\Traits\EnumTrait;

enum CombustivelEnum: string
{
    use EnumTrait;

    case FLEX = 'flex';
    case GASOLINA = 'gasolina';
    case ETANOL = 'etanol';
    case DIESEL = 'diesel';
    case ELETRICO = 'eletrico';
    case HIBRIDO = 'hibrido';

    public function label(): string
    {
        return match ($this) {
            self::FLEX => 'Flex',
            self::GASOLINA => 'Gasolina',
            self::ETANOL => 'Etanol',
            self::DIESEL => 'Diesel',
            self::ELETRICO => 'Elétrico',
            self::HIBRIDO => 'Híbrido',
        };
    }
}
