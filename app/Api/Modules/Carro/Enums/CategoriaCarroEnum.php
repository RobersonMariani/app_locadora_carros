<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Enums;

use App\Api\Support\Traits\EnumTrait;

enum CategoriaCarroEnum: string
{
    use EnumTrait;

    case ECONOMICO = 'economico';
    case COMPACTO = 'compacto';
    case SEDAN = 'sedan';
    case SUV = 'suv';
    case PICKUP = 'pickup';
    case LUXO = 'luxo';
    case VAN = 'van';

    public function label(): string
    {
        return match ($this) {
            self::ECONOMICO => 'Econômico',
            self::COMPACTO => 'Compacto',
            self::SEDAN => 'Sedan',
            self::SUV => 'SUV',
            self::PICKUP => 'Pickup',
            self::LUXO => 'Luxo',
            self::VAN => 'Van',
        };
    }
}
