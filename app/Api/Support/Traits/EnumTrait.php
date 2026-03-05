<?php

declare(strict_types=1);

namespace App\Api\Support\Traits;

trait EnumTrait
{
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function options(): array
    {
        $result = [];

        foreach (static::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }

        return $result;
    }

    public function equals(self ...$enums): bool
    {
        foreach ($enums as $enum) {
            if ($this === $enum) {
                return true;
            }
        }

        return false;
    }

    public static function toArray(): array
    {
        $result = [];

        foreach (static::cases() as $case) {
            $result[$case->name] = $case->value;
        }

        return $result;
    }
}
