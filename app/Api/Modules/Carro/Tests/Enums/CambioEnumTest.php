<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Enums;

use App\Api\Modules\Carro\Enums\CambioEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CambioEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'manual_label' => [CambioEnum::MANUAL, 'Manual'],
            'automatico_label' => [CambioEnum::AUTOMATICO, 'Automático'],
            'cvt_label' => [CambioEnum::CVT, 'CVT'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        CambioEnum $enum,
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
        $result = CambioEnum::values();

        // Assert
        $expected = ['manual', 'automatico', 'cvt'];
        $this->assertEquals($expected, $result);
    }
}
