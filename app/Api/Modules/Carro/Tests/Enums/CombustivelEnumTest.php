<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Enums;

use App\Api\Modules\Carro\Enums\CombustivelEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CombustivelEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'flex_label' => [CombustivelEnum::FLEX, 'Flex'],
            'gasolina_label' => [CombustivelEnum::GASOLINA, 'Gasolina'],
            'etanol_label' => [CombustivelEnum::ETANOL, 'Etanol'],
            'diesel_label' => [CombustivelEnum::DIESEL, 'Diesel'],
            'eletrico_label' => [CombustivelEnum::ELETRICO, 'Elétrico'],
            'hibrido_label' => [CombustivelEnum::HIBRIDO, 'Híbrido'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        CombustivelEnum $enum,
        string $expectedLabel,
    ): void {
        // Act
        $result = $enum->label();

        // Assert
        $this->assertEquals($expectedLabel, $result);
    }

    public function testValuesShouldReturnAllEnumValuesWhenCalled(): void
    {
        // Act
        $result = CombustivelEnum::values();

        // Assert
        $expected = ['flex', 'gasolina', 'etanol', 'diesel', 'eletrico', 'hibrido'];
        $this->assertEquals($expected, $result);
    }
}
