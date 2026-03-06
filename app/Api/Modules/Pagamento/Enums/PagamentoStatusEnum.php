<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Enums;

use App\Api\Support\Traits\EnumTrait;

enum PagamentoStatusEnum: string
{
    use EnumTrait;

    case PENDENTE = 'pendente';
    case PAGO = 'pago';
    case CANCELADO = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::PAGO => 'Pago',
            self::CANCELADO => 'Cancelado',
        };
    }
}
