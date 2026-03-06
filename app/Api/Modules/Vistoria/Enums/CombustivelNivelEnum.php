<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Enums;

use App\Api\Support\Traits\EnumTrait;

enum CombustivelNivelEnum: string
{
    use EnumTrait;

    case VAZIO = 'vazio';
    case UM_QUARTO = '1_4';
    case METADE = 'metade';
    case TRES_QUARTOS = '3_4';
    case CHEIO = 'cheio';

    public function label(): string
    {
        return match ($this) {
            self::VAZIO => 'Vazio',
            self::UM_QUARTO => '1/4',
            self::METADE => 'Metade',
            self::TRES_QUARTOS => '3/4',
            self::CHEIO => 'Cheio',
        };
    }
}
