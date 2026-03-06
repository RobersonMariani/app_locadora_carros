<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\Enums;

use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class VistoriaTipoEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'retirada_label' => [VistoriaTipoEnum::RETIRADA, 'Retirada'],
            'devolucao_label' => [VistoriaTipoEnum::DEVOLUCAO, 'Devolução'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        VistoriaTipoEnum $enum,
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
        $result = VistoriaTipoEnum::values();

        // Assert
        $this->assertEquals(['retirada', 'devolucao'], $result);
    }
}
