<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Data;

use App\Api\Modules\Pagamento\Data\UpdatePagamentoData;
use App\Models\Locacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class UpdatePagamentoDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validData(): array
    {
        return [
            'empty_updates' => [[]],
            'update_valor' => [['valor' => 200.00]],
            'update_tipo' => [['tipo' => 'multa_atraso']],
            'update_metodo_pagamento' => [['metodo_pagamento' => 'credito']],
            'update_data_pagamento' => [['data_pagamento' => '2024-02-20']],
            'update_observacoes' => [['observacoes' => 'Atualizado']],
            'update_locacao_id' => [['locacao_id' => 1]],
            'update_status_pago' => [['status' => 'pago']],
            'update_status_cancelado' => [['status' => 'cancelado']],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'valor_zero' => [['valor' => 0], 'valor'],
            'valor_negative' => [['valor' => -10], 'valor'],
            'tipo_invalid' => [['tipo' => 'invalido'], 'tipo'],
            'metodo_pagamento_invalid' => [['metodo_pagamento' => 'invalido'], 'metodo_pagamento'],
            'data_pagamento_invalid' => [['data_pagamento' => 'invalid'], 'data_pagamento'],
            'observacoes_too_long' => [['observacoes' => str_repeat('a', 501)], 'observacoes'],
            'locacao_id_not_exists' => [['locacao_id' => 99999], 'locacao_id'],
            'status_invalid' => [['status' => 'invalido'], 'status'],
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
        $result = UpdatePagamentoData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(UpdatePagamentoData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        if (isset($invalidItem['locacao_id']) && $invalidItem['locacao_id'] === 99999) {
            $invalidItem['locacao_id'] = 99999;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdatePagamentoData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
