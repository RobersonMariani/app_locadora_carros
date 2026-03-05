<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Enums;

use App\Api\Support\Traits\EnumTrait;

enum PagamentoTipoEnum: string
{
    use EnumTrait;

    case DIARIA = 'diaria';
    case MULTA_ATRASO = 'multa_atraso';
    case KM_EXTRA = 'km_extra';
    case DANO = 'dano';
    case DESCONTO = 'desconto';

    public function label(): string
    {
        return match ($this) {
            self::DIARIA => 'Diária',
            self::MULTA_ATRASO => 'Multa por atraso',
            self::KM_EXTRA => 'Km extra',
            self::DANO => 'Dano',
            self::DESCONTO => 'Desconto',
        };
    }
}
