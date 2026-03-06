<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Enums;

use App\Api\Support\Traits\EnumTrait;

enum AlertaTipoEnum: string
{
    use EnumTrait;

    case LOCACAO_ATRASADA = 'locacao_atrasada';
    case MANUTENCAO_PROXIMA = 'manutencao_proxima';
    case MANUTENCAO_VENCIDA = 'manutencao_vencida';
    case MULTA_PENDENTE = 'multa_pendente';
    case INADIMPLENCIA = 'inadimplencia';

    public function label(): string
    {
        return match ($this) {
            self::LOCACAO_ATRASADA => 'Locação atrasada',
            self::MANUTENCAO_PROXIMA => 'Manutenção próxima',
            self::MANUTENCAO_VENCIDA => 'Manutenção vencida',
            self::MULTA_PENDENTE => 'Multa pendente',
            self::INADIMPLENCIA => 'Inadimplência',
        };
    }
}
