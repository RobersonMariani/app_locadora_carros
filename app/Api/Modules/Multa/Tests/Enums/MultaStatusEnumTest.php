<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Enums;

use App\Api\Modules\Multa\Enums\MultaStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class MultaStatusEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'pendente_label' => [MultaStatusEnum::PENDENTE, 'Pendente'],
            'paga_label' => [MultaStatusEnum::PAGA, 'Paga'],
            'contestada_label' => [MultaStatusEnum::CONTESTADA, 'Contestada'],
            'cancelada_label' => [MultaStatusEnum::CANCELADA, 'Cancelada'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        MultaStatusEnum $enum,
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
        $result = MultaStatusEnum::values();

        // Assert
        $expected = ['pendente', 'paga', 'contestada', 'cancelada'];
        $this->assertEquals($expected, $result);
    }
}
