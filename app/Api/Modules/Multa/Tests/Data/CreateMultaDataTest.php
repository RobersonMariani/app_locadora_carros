<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Data;

use App\Api\Modules\Multa\Data\CreateMultaData;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class CreateMultaDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'locacao_id' => 1,
            'carro_id' => 1,
            'cliente_id' => 1,
            'valor' => 150.50,
            'data_infracao' => '2024-01-15',
            'descricao' => 'Excesso de velocidade',
            'status' => 'pendente',
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'descricao_max_length' => [array_merge(self::validPayload(), ['descricao' => str_repeat('a', 255)])],
            'with_codigo_infracao' => [array_merge(self::validPayload(), ['codigo_infracao' => '12345'])],
            'with_pontos' => [array_merge(self::validPayload(), ['pontos' => 5])],
            'with_data_pagamento' => [array_merge(self::validPayload(), ['data_pagamento' => '2024-02-01'])],
            'with_observacoes' => [array_merge(self::validPayload(), ['observacoes' => 'Cliente contestou'])],
            'status_paga' => [array_merge(self::validPayload(), ['status' => 'paga'])],
            'status_contestada' => [array_merge(self::validPayload(), ['status' => 'contestada'])],
            'status_cancelada' => [array_merge(self::validPayload(), ['status' => 'cancelada'])],
            'valor_min' => [array_merge(self::validPayload(), ['valor' => 0.01])],
            'pontos_max' => [array_merge(self::validPayload(), ['pontos' => 21])],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'locacao_id_missing' => [collect(self::validPayload())->except('locacao_id')->toArray(), 'locacao_id'],
            'locacao_id_not_exists' => [array_merge(self::validPayload(), ['locacao_id' => 99999]), 'locacao_id'],
            'locacao_id_not_integer' => [array_merge(self::validPayload(), ['locacao_id' => 'abc']), 'locacao_id'],
            'carro_id_missing' => [collect(self::validPayload())->except('carro_id')->toArray(), 'carro_id'],
            'carro_id_not_exists' => [array_merge(self::validPayload(), ['carro_id' => 99999]), 'carro_id'],
            'cliente_id_missing' => [collect(self::validPayload())->except('cliente_id')->toArray(), 'cliente_id'],
            'cliente_id_not_exists' => [array_merge(self::validPayload(), ['cliente_id' => 99999]), 'cliente_id'],
            'valor_missing' => [collect(self::validPayload())->except('valor')->toArray(), 'valor'],
            'valor_negative' => [array_merge(self::validPayload(), ['valor' => -10]), 'valor'],
            'valor_zero' => [array_merge(self::validPayload(), ['valor' => 0]), 'valor'],
            'data_infracao_missing' => [collect(self::validPayload())->except('data_infracao')->toArray(), 'data_infracao'],
            'data_infracao_invalid' => [array_merge(self::validPayload(), ['data_infracao' => 'invalid']), 'data_infracao'],
            'descricao_missing' => [collect(self::validPayload())->except('descricao')->toArray(), 'descricao'],
            'descricao_empty' => [array_merge(self::validPayload(), ['descricao' => '']), 'descricao'],
            'descricao_too_long' => [array_merge(self::validPayload(), ['descricao' => str_repeat('a', 256)]), 'descricao'],
            'status_missing' => [collect(self::validPayload())->except('status')->toArray(), 'status'],
            'status_invalid' => [array_merge(self::validPayload(), ['status' => 'invalido']), 'status'],
            'codigo_infracao_too_long' => [array_merge(self::validPayload(), ['codigo_infracao' => str_repeat('a', 21)]), 'codigo_infracao'],
            'pontos_negative' => [array_merge(self::validPayload(), ['pontos' => -1]), 'pontos'],
            'pontos_exceeds_max' => [array_merge(self::validPayload(), ['pontos' => 22]), 'pontos'],
            'observacoes_too_long' => [array_merge(self::validPayload(), ['observacoes' => str_repeat('a', 1001)]), 'observacoes'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $locacao = $this->createLocacaoWithRelations();

        if (isset($validItem['locacao_id']) && $validItem['locacao_id'] === 1) {
            $validItem['locacao_id'] = $locacao->id;
        }

        if (isset($validItem['carro_id']) && $validItem['carro_id'] === 1) {
            $validItem['carro_id'] = $locacao->carro_id;
        }

        if (isset($validItem['cliente_id']) && $validItem['cliente_id'] === 1) {
            $validItem['cliente_id'] = $locacao->cliente_id;
        }

        // Act
        $result = CreateMultaData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CreateMultaData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $locacao = $this->createLocacaoWithRelations();

        if ($expectedField !== 'locacao_id' && isset($invalidItem['locacao_id']) && is_int($invalidItem['locacao_id'])) {
            $invalidItem['locacao_id'] = $locacao->id;
        }

        if ($expectedField !== 'carro_id' && isset($invalidItem['carro_id']) && is_int($invalidItem['carro_id'])) {
            $invalidItem['carro_id'] = $locacao->carro_id;
        }

        if ($expectedField !== 'cliente_id' && isset($invalidItem['cliente_id']) && is_int($invalidItem['cliente_id'])) {
            $invalidItem['cliente_id'] = $locacao->cliente_id;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateMultaData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    private function createLocacaoWithRelations(): Locacao
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);
        $cliente = Cliente::factory()->create();

        return Locacao::factory()->create([
            'carro_id' => $carro->id,
            'cliente_id' => $cliente->id,
        ]);
    }
}
