<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Data;

use App\Api\Modules\Cliente\Data\UpdateClienteData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class UpdateClienteDataTest extends TestCase
{
    private static function validPayload(): array
    {
        return [
            'nome' => 'Maria Santos',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'nome_max_length' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 100)])],
            'bloqueado_true_with_motivo' => [array_merge(self::validPayload(), [
                'bloqueado' => true,
                'motivo_bloqueio' => 'Inadimplência',
            ])],
            'bloqueado_false' => [array_merge(self::validPayload(), ['bloqueado' => false])],
            'bloqueado_true_motivo_max_length' => [array_merge(self::validPayload(), [
                'bloqueado' => true,
                'motivo_bloqueio' => str_repeat('a', 255),
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
            'nome_too_short' => [array_merge(self::validPayload(), ['nome' => 'AB']), 'nome'],
            'nome_too_long' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 101)]), 'nome'],
            'nome_not_string' => [array_merge(self::validPayload(), ['nome' => 123]), 'nome'],
            'email_invalid' => [array_merge(self::validPayload(), ['email' => 'invalid-email']), 'email'],
            'cpf_invalid_format' => [array_merge(self::validPayload(), ['cpf' => '12345678900']), 'cpf'],
            'telefone_invalid_format' => [array_merge(self::validPayload(), ['telefone' => '11999999999']), 'telefone'],
            'cnh_invalid_format' => [array_merge(self::validPayload(), ['cnh' => 'ABC']), 'cnh'],
            'bloqueado_true_without_motivo' => [array_merge(self::validPayload(), ['bloqueado' => true]), 'motivo_bloqueio'],
            'bloqueado_true_motivo_empty' => [array_merge(self::validPayload(), [
                'bloqueado' => true,
                'motivo_bloqueio' => '',
            ]), 'motivo_bloqueio'],
            'bloqueado_true_motivo_too_long' => [array_merge(self::validPayload(), [
                'bloqueado' => true,
                'motivo_bloqueio' => str_repeat('a', 256),
            ]), 'motivo_bloqueio'],
            'estado_invalid_format' => [array_merge(self::validPayload(), ['estado' => 'S']), 'estado'],
            'cep_invalid_format' => [array_merge(self::validPayload(), ['cep' => '01310100']), 'cep'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange & Act
        $result = UpdateClienteData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(UpdateClienteData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateClienteData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
