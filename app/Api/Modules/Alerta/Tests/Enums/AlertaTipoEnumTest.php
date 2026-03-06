<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Tests\Enums;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('alerta')]
class AlertaTipoEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'locacao_atrasada_label' => [AlertaTipoEnum::LOCACAO_ATRASADA, 'Locação atrasada'],
            'manutencao_proxima_label' => [AlertaTipoEnum::MANUTENCAO_PROXIMA, 'Manutenção próxima'],
            'manutencao_vencida_label' => [AlertaTipoEnum::MANUTENCAO_VENCIDA, 'Manutenção vencida'],
            'multa_pendente_label' => [AlertaTipoEnum::MULTA_PENDENTE, 'Multa pendente'],
            'inadimplencia_label' => [AlertaTipoEnum::INADIMPLENCIA, 'Inadimplência'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        AlertaTipoEnum $enum,
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
        $result = AlertaTipoEnum::values();

        // Assert
        $expected = [
            'locacao_atrasada',
            'manutencao_proxima',
            'manutencao_vencida',
            'multa_pendente',
            'inadimplencia',
        ];
        $this->assertEquals($expected, $result);
    }
}
