<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Enums;

use App\Api\Modules\Manutencao\Enums\ManutencaoTipoEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class ManutencaoTipoEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'preventiva_label' => [ManutencaoTipoEnum::PREVENTIVA, 'Preventiva'],
            'corretiva_label' => [ManutencaoTipoEnum::CORRETIVA, 'Corretiva'],
            'revisao_label' => [ManutencaoTipoEnum::REVISAO, 'Revisão'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        ManutencaoTipoEnum $enum,
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
        $result = ManutencaoTipoEnum::values();

        // Assert
        $expected = ['preventiva', 'corretiva', 'revisao'];
        $this->assertEquals($expected, $result);
    }
}
