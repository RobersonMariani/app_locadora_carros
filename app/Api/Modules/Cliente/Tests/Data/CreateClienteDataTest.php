<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Data;

use App\Api\Modules\Cliente\Data\CreateClienteData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class CreateClienteDataTest extends TestCase
{
    private static function validPayload(): array
    {
        return [
            'nome' => 'João Silva',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'nome_max_length' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 30)])],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'nome_null' => [array_merge(self::validPayload(), ['nome' => null]), 'nome'],
            'nome_empty' => [array_merge(self::validPayload(), ['nome' => '']), 'nome'],
            'nome_too_long' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 31)]), 'nome'],
            'nome_not_string' => [array_merge(self::validPayload(), ['nome' => 123]), 'nome'],
            'nome_missing' => [[], 'nome'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange & Act
        $result = CreateClienteData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CreateClienteData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateClienteData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
