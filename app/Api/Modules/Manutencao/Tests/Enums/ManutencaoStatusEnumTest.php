<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Enums;

use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class ManutencaoStatusEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'agendada_label' => [ManutencaoStatusEnum::AGENDADA, 'Agendada'],
            'em_andamento_label' => [ManutencaoStatusEnum::EM_ANDAMENTO, 'Em Andamento'],
            'concluida_label' => [ManutencaoStatusEnum::CONCLUIDA, 'Concluída'],
            'cancelada_label' => [ManutencaoStatusEnum::CANCELADA, 'Cancelada'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        ManutencaoStatusEnum $enum,
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
        $result = ManutencaoStatusEnum::values();

        // Assert
        $expected = ['agendada', 'em_andamento', 'concluida', 'cancelada'];
        $this->assertEquals($expected, $result);
    }
}
