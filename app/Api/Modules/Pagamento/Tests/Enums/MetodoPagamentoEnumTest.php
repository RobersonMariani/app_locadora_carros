<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Enums;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class MetodoPagamentoEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'dinheiro_label' => [MetodoPagamentoEnum::DINHEIRO, 'Dinheiro'],
            'credito_label' => [MetodoPagamentoEnum::CREDITO, 'Crédito'],
            'debito_label' => [MetodoPagamentoEnum::DEBITO, 'Débito'],
            'pix_label' => [MetodoPagamentoEnum::PIX, 'PIX'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        MetodoPagamentoEnum $enum,
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
        $result = MetodoPagamentoEnum::values();

        // Assert
        $expected = ['dinheiro', 'credito', 'debito', 'pix'];
        $this->assertEquals($expected, $result);
    }
}
