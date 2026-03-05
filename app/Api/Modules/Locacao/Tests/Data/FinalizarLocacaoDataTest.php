<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Data;

use App\Api\Modules\Locacao\Data\FinalizarLocacaoData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class FinalizarLocacaoDataTest extends TestCase
{
    private static function validPayload(): array
    {
        return [
            'km_final' => 55000,
            'data_final_realizado_periodo' => '2024-01-20',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'km_zero' => [array_merge(self::validPayload(), ['km_final' => 0])],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'km_final_null' => [array_merge(self::validPayload(), ['km_final' => null]), 'km_final'],
            'km_final_missing' => [collect(self::validPayload())->except('km_final')->toArray(), 'km_final'],
            'km_final_negative' => [array_merge(self::validPayload(), ['km_final' => -1]), 'km_final'],
            'km_final_not_integer' => [array_merge(self::validPayload(), ['km_final' => 1.5]), 'km_final'],
            'data_final_realizado_periodo_null' => [array_merge(self::validPayload(), ['data_final_realizado_periodo' => null]), 'data_final_realizado_periodo'],
            'data_final_realizado_periodo_missing' => [collect(self::validPayload())->except('data_final_realizado_periodo')->toArray(), 'data_final_realizado_periodo'],
            'data_final_realizado_periodo_invalid' => [array_merge(self::validPayload(), ['data_final_realizado_periodo' => 'invalid']), 'data_final_realizado_periodo'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange & Act
        $result = FinalizarLocacaoData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(FinalizarLocacaoData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            FinalizarLocacaoData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
