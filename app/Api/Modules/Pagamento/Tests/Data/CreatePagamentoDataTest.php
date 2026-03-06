<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Data;

use App\Api\Modules\Pagamento\Data\CreatePagamentoData;
use App\Models\Locacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class CreatePagamentoDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'locacao_id' => 1,
            'valor' => 100.50,
            'tipo' => 'diaria',
            'metodo_pagamento' => 'pix',
            'data_pagamento' => '2024-01-15',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'valor_min' => [array_merge(self::validPayload(), ['valor' => 0.01])],
            'with_observacoes' => [array_merge(self::validPayload(), ['observacoes' => 'Pagamento em dia'])],
            'observacoes_max_length' => [array_merge(self::validPayload(), ['observacoes' => str_repeat('a', 500)])],
            'with_status_pendente' => [array_merge(self::validPayload(), ['status' => 'pendente'])],
            'with_status_pago' => [array_merge(self::validPayload(), ['status' => 'pago'])],
            'with_status_cancelado' => [array_merge(self::validPayload(), ['status' => 'cancelado'])],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'locacao_id_null' => [array_merge(self::validPayload(), ['locacao_id' => null]), 'locacao_id'],
            'locacao_id_missing' => [collect(self::validPayload())->except('locacao_id')->toArray(), 'locacao_id'],
            'locacao_id_not_exists' => [array_merge(self::validPayload(), ['locacao_id' => 99999]), 'locacao_id'],
            'valor_null' => [array_merge(self::validPayload(), ['valor' => null]), 'valor'],
            'valor_zero' => [array_merge(self::validPayload(), ['valor' => 0]), 'valor'],
            'valor_negative' => [array_merge(self::validPayload(), ['valor' => -1]), 'valor'],
            'tipo_null' => [array_merge(self::validPayload(), ['tipo' => null]), 'tipo'],
            'tipo_invalid' => [array_merge(self::validPayload(), ['tipo' => 'invalido']), 'tipo'],
            'metodo_pagamento_null' => [array_merge(self::validPayload(), ['metodo_pagamento' => null]), 'metodo_pagamento'],
            'metodo_pagamento_invalid' => [array_merge(self::validPayload(), ['metodo_pagamento' => 'invalido']), 'metodo_pagamento'],
            'data_pagamento_null' => [array_merge(self::validPayload(), ['data_pagamento' => null]), 'data_pagamento'],
            'data_pagamento_invalid' => [array_merge(self::validPayload(), ['data_pagamento' => 'invalid-date']), 'data_pagamento'],
            'observacoes_too_long' => [array_merge(self::validPayload(), ['observacoes' => str_repeat('a', 501)]), 'observacoes'],
            'status_invalid' => [array_merge(self::validPayload(), ['status' => 'invalido']), 'status'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        if (isset($validItem['locacao_id']) && $validItem['locacao_id'] === 1) {
            $validItem['locacao_id'] = $locacao->id;
        }

        // Act
        $result = CreatePagamentoData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CreatePagamentoData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        if (isset($invalidItem['locacao_id']) && $invalidItem['locacao_id'] === 1) {
            $invalidItem['locacao_id'] = $locacao->id;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreatePagamentoData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
