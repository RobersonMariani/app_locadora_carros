<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Data;

use App\Api\Modules\Cliente\Data\CreateClienteData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class CreateClienteDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'nome' => 'João Silva',
            'cpf' => '123.456.789-00',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'nome_max_length' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 100)])],
            'with_optional_fields' => [array_merge(self::validPayload(), [
                'email' => 'test@example.com',
                'telefone' => '(11) 99999-9999',
                'data_nascimento' => '1990-01-15',
                'cnh' => '12345678901',
            ])],
            'with_endereco_fields' => [array_merge(self::validPayload(), [
                'endereco' => 'Rua das Flores, 123',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01310-100',
            ])],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'nome_null' => [array_merge(self::validPayload(), ['nome' => null]), 'nome'],
            'nome_empty' => [array_merge(self::validPayload(), ['nome' => '']), 'nome'],
            'nome_too_short' => [array_merge(self::validPayload(), ['nome' => 'AB']), 'nome'],
            'nome_too_long' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 101)]), 'nome'],
            'nome_not_string' => [array_merge(self::validPayload(), ['nome' => 123]), 'nome'],
            'nome_missing' => [collect(self::validPayload())->except('nome')->toArray(), 'nome'],
            'cpf_null' => [array_merge(self::validPayload(), ['cpf' => null]), 'cpf'],
            'cpf_empty' => [array_merge(self::validPayload(), ['cpf' => '']), 'cpf'],
            'cpf_invalid_format' => [array_merge(self::validPayload(), ['cpf' => '12345678900']), 'cpf'],
            'cpf_missing' => [collect(self::validPayload())->except('cpf')->toArray(), 'cpf'],
            'telefone_invalid_format' => [array_merge(self::validPayload(), ['telefone' => '11999999999']), 'telefone'],
            'cnh_invalid_format' => [array_merge(self::validPayload(), ['cnh' => '123']), 'cnh'],
            'estado_invalid_format' => [array_merge(self::validPayload(), ['estado' => 'S']), 'estado'],
            'estado_invalid_format_long' => [array_merge(self::validPayload(), ['estado' => 'SPP']), 'estado'],
            'estado_invalid_lowercase' => [array_merge(self::validPayload(), ['estado' => 'sp']), 'estado'],
            'cep_invalid_format' => [array_merge(self::validPayload(), ['cep' => '01310100']), 'cep'],
            'cep_invalid_format_without_dash' => [array_merge(self::validPayload(), ['cep' => '01310-1000']), 'cep'],
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
