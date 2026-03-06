<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\Enums;

use App\Api\Modules\Vistoria\Enums\CombustivelNivelEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class CombustivelNivelEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'vazio_label' => [CombustivelNivelEnum::VAZIO, 'Vazio'],
            '1_4_label' => [CombustivelNivelEnum::UM_QUARTO, '1/4'],
            'metade_label' => [CombustivelNivelEnum::METADE, 'Metade'],
            '3_4_label' => [CombustivelNivelEnum::TRES_QUARTOS, '3/4'],
            'cheio_label' => [CombustivelNivelEnum::CHEIO, 'Cheio'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        CombustivelNivelEnum $enum,
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
        $result = CombustivelNivelEnum::values();

        // Assert
        $this->assertEquals(['vazio', '1_4', 'metade', '3_4', 'cheio'], $result);
    }
}
