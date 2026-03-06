<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Data;

use App\Api\Modules\Multa\Data\UpdateMultaData;
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
class UpdateMultaDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validDataProvider(): array
    {
        return [
            'empty_payload' => [[]],
            'only_valor' => [['valor' => 200.00]],
            'only_descricao' => [['descricao' => 'Nova descrição']],
            'only_status' => [['status' => 'paga']],
            'only_data_pagamento' => [['data_pagamento' => '2024-03-01']],
            'only_observacoes' => [['observacoes' => 'Atualizado']],
            'only_locacao_id' => [['locacao_id' => 1]],
            'only_carro_id' => [['carro_id' => 1]],
            'only_cliente_id' => [['cliente_id' => 1]],
            'only_data_infracao' => [['data_infracao' => '2024-02-15']],
            'only_codigo_infracao' => [['codigo_infracao' => '98765']],
            'only_pontos' => [['pontos' => 7]],
            'all_fields' => [
                [
                    'locacao_id' => 1,
                    'carro_id' => 1,
                    'cliente_id' => 1,
                    'valor' => 180.50,
                    'data_infracao' => '2024-02-20',
                    'descricao' => 'Descrição atualizada',
                    'codigo_infracao' => '11111',
                    'pontos' => 4,
                    'status' => 'contestada',
                    'data_pagamento' => '2024-03-15',
                    'observacoes' => 'Em análise',
                ],
            ],
            'descricao_max_length' => [['descricao' => str_repeat('a', 255)]],
            'valor_min' => [['valor' => 0.01]],
            'pontos_max' => [['pontos' => 21]],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'locacao_id_not_exists' => [['locacao_id' => 99999], 'locacao_id'],
            'carro_id_not_exists' => [['carro_id' => 99999], 'carro_id'],
            'cliente_id_not_exists' => [['cliente_id' => 99999], 'cliente_id'],
            'valor_negative' => [['valor' => -50], 'valor'],
            'valor_zero' => [['valor' => 0], 'valor'],
            'data_infracao_invalid' => [['data_infracao' => 'invalid'], 'data_infracao'],
            'descricao_too_long' => [['descricao' => str_repeat('a', 256)], 'descricao'],
            'codigo_infracao_too_long' => [['codigo_infracao' => str_repeat('a', 21)], 'codigo_infracao'],
            'pontos_negative' => [['pontos' => -1], 'pontos'],
            'pontos_exceeds_max' => [['pontos' => 22], 'pontos'],
            'status_invalid' => [['status' => 'invalido'], 'status'],
            'observacoes_too_long' => [['observacoes' => str_repeat('a', 1001)], 'observacoes'],
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
        $result = UpdateMultaData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(UpdateMultaData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $locacao = $this->createLocacaoWithRelations();

        if (isset($invalidItem['locacao_id']) && $invalidItem['locacao_id'] !== 99999) {
            $invalidItem['locacao_id'] = $locacao->id;
        }

        if (isset($invalidItem['carro_id']) && $invalidItem['carro_id'] !== 99999) {
            $invalidItem['carro_id'] = $locacao->carro_id;
        }

        if (isset($invalidItem['cliente_id']) && $invalidItem['cliente_id'] !== 99999) {
            $invalidItem['cliente_id'] = $locacao->cliente_id;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateMultaData::validateAndCreate($invalidItem);
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
