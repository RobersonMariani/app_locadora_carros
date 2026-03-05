<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Enums;

use App\Api\Support\Traits\EnumTrait;

enum MetodoPagamentoEnum: string
{
    use EnumTrait;

    case DINHEIRO = 'dinheiro';
    case CREDITO = 'credito';
    case DEBITO = 'debito';
    case PIX = 'pix';

    public function label(): string
    {
        return match ($this) {
            self::DINHEIRO => 'Dinheiro',
            self::CREDITO => 'Crédito',
            self::DEBITO => 'Débito',
            self::PIX => 'PIX',
        };
    }
}
