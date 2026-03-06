<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Enums;

use App\Api\Modules\Carro\Enums\CategoriaCarroEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CategoriaCarroEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'economico_label' => [CategoriaCarroEnum::ECONOMICO, 'Econômico'],
            'compacto_label' => [CategoriaCarroEnum::COMPACTO, 'Compacto'],
            'sedan_label' => [CategoriaCarroEnum::SEDAN, 'Sedan'],
            'suv_label' => [CategoriaCarroEnum::SUV, 'SUV'],
            'pickup_label' => [CategoriaCarroEnum::PICKUP, 'Pickup'],
            'luxo_label' => [CategoriaCarroEnum::LUXO, 'Luxo'],
            'van_label' => [CategoriaCarroEnum::VAN, 'Van'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        CategoriaCarroEnum $enum,
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
        $result = CategoriaCarroEnum::values();

        // Assert
        $expected = ['economico', 'compacto', 'sedan', 'suv', 'pickup', 'luxo', 'van'];
        $this->assertEquals($expected, $result);
    }
}
