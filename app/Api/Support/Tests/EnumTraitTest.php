<?php

declare(strict_types=1);

namespace App\Api\Support\Tests;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('support')]
class EnumTraitTest extends TestCase
{
    public function testValuesShouldReturnArrayOfEnumValues(): void
    {
        // Act
        $result = PagamentoTipoEnum::values();

        // Assert
        $this->assertIsArray($result);
        $this->assertContains('diaria', $result);
        $this->assertContains('pix', MetodoPagamentoEnum::values());
    }

    public function testNamesShouldReturnArrayOfEnumNames(): void
    {
        // Act
        $result = PagamentoTipoEnum::names();

        // Assert
        $this->assertIsArray($result);
        $this->assertContains('DIARIA', $result);
        $this->assertContains('PIX', MetodoPagamentoEnum::names());
    }

    public function testToArrayShouldReturnNameToValueMap(): void
    {
        // Act
        $result = PagamentoTipoEnum::toArray();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('DIARIA', $result);
        $this->assertEquals('diaria', $result['DIARIA']);
    }

    public function testOptionsShouldReturnArrayWithValueAndLabel(): void
    {
        // Act
        $result = PagamentoTipoEnum::options();

        // Assert
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $first = $result[0];
        $this->assertArrayHasKey('value', $first);
        $this->assertArrayHasKey('label', $first);
    }

    public function testEqualsShouldReturnTrueWhenEnumMatches(): void
    {
        // Act & Assert
        $this->assertTrue(PagamentoTipoEnum::DIARIA->equals(PagamentoTipoEnum::DIARIA));
        $this->assertTrue(MetodoPagamentoEnum::PIX->equals(MetodoPagamentoEnum::PIX));
    }

    public function testEqualsShouldReturnTrueWhenOneOfGivenEnumsMatches(): void
    {
        // Act & Assert - equals accepts variadic
        $this->assertTrue(PagamentoTipoEnum::DIARIA->equals(PagamentoTipoEnum::MULTA_ATRASO, PagamentoTipoEnum::DIARIA));
    }

    public function testEqualsShouldReturnFalseWhenNoMatch(): void
    {
        // Act & Assert
        $this->assertFalse(PagamentoTipoEnum::DIARIA->equals(PagamentoTipoEnum::MULTA_ATRASO));
    }
}
