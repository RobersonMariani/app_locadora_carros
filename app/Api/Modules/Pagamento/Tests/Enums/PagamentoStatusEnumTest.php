<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Enums;

use App\Api\Modules\Pagamento\Enums\PagamentoStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class PagamentoStatusEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'pendente_label' => [PagamentoStatusEnum::PENDENTE, 'Pendente'],
            'pago_label' => [PagamentoStatusEnum::PAGO, 'Pago'],
            'cancelado_label' => [PagamentoStatusEnum::CANCELADO, 'Cancelado'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        PagamentoStatusEnum $enum,
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
        $result = PagamentoStatusEnum::values();

        // Assert
        $expected = ['pendente', 'pago', 'cancelado'];
        $this->assertEquals($expected, $result);
    }
}
