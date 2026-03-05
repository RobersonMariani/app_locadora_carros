<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Enums;

use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class PagamentoTipoEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'diaria_label' => [PagamentoTipoEnum::DIARIA, 'Diária'],
            'multa_atraso_label' => [PagamentoTipoEnum::MULTA_ATRASO, 'Multa por atraso'],
            'km_extra_label' => [PagamentoTipoEnum::KM_EXTRA, 'Km extra'],
            'dano_label' => [PagamentoTipoEnum::DANO, 'Dano'],
            'desconto_label' => [PagamentoTipoEnum::DESCONTO, 'Desconto'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        PagamentoTipoEnum $enum,
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
        $result = PagamentoTipoEnum::values();

        // Assert
        $expected = ['diaria', 'multa_atraso', 'km_extra', 'dano', 'desconto'];
        $this->assertEquals($expected, $result);
    }
}
