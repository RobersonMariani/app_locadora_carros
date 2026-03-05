<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Enums;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class LocacaoStatusEnumTest extends TestCase
{
    public static function labelProvider(): array
    {
        return [
            'reservada_label' => [LocacaoStatusEnum::RESERVADA, 'Reservada'],
            'ativa_label' => [LocacaoStatusEnum::ATIVA, 'Ativa'],
            'finalizada_label' => [LocacaoStatusEnum::FINALIZADA, 'Finalizada'],
            'cancelada_label' => [LocacaoStatusEnum::CANCELADA, 'Cancelada'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testLabelShouldReturnCorrectTranslationWhenCalled(
        LocacaoStatusEnum $enum,
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
        $result = LocacaoStatusEnum::values();

        // Assert
        $expected = ['reservada', 'ativa', 'finalizada', 'cancelada'];
        $this->assertEquals($expected, $result);
    }

    public function testCanTransitionToShouldAllowReservadaToAtiva(): void
    {
        // Act & Assert
        $this->assertTrue(LocacaoStatusEnum::RESERVADA->canTransitionTo(LocacaoStatusEnum::ATIVA));
    }

    public function testCanTransitionToShouldAllowReservadaToCancelada(): void
    {
        // Act & Assert
        $this->assertTrue(LocacaoStatusEnum::RESERVADA->canTransitionTo(LocacaoStatusEnum::CANCELADA));
    }

    public function testCanTransitionToShouldAllowAtivaToFinalizada(): void
    {
        // Act & Assert
        $this->assertTrue(LocacaoStatusEnum::ATIVA->canTransitionTo(LocacaoStatusEnum::FINALIZADA));
    }

    public function testCanTransitionToShouldAllowAtivaToCancelada(): void
    {
        // Act & Assert
        $this->assertTrue(LocacaoStatusEnum::ATIVA->canTransitionTo(LocacaoStatusEnum::CANCELADA));
    }

    public function testCanTransitionToShouldDenyFinalizadaToAny(): void
    {
        // Act & Assert
        $this->assertFalse(LocacaoStatusEnum::FINALIZADA->canTransitionTo(LocacaoStatusEnum::ATIVA));
        $this->assertFalse(LocacaoStatusEnum::FINALIZADA->canTransitionTo(LocacaoStatusEnum::RESERVADA));
    }

    public function testCanTransitionToShouldDenyReservadaToFinalizada(): void
    {
        // Act & Assert
        $this->assertFalse(LocacaoStatusEnum::RESERVADA->canTransitionTo(LocacaoStatusEnum::FINALIZADA));
    }
}
