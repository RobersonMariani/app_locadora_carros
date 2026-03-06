<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Enums;

use App\Api\Support\Traits\EnumTrait;

enum CambioEnum: string
{
    use EnumTrait;

    case MANUAL = 'manual';
    case AUTOMATICO = 'automatico';
    case CVT = 'cvt';

    public function label(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::AUTOMATICO => 'Automático',
            self::CVT => 'CVT',
        };
    }
}
