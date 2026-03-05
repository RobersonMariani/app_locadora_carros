<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Data;

use App\Api\Modules\Pagamento\Data\PagamentoQueryData;
use App\Models\Locacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class PagamentoQueryDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validData(): array
    {
        return [
            'empty_query' => [[]],
            'with_locacao_id' => [['locacao_id' => 1]],
            'with_tipo' => [['tipo' => 'diaria']],
            'with_metodo_pagamento' => [['metodo_pagamento' => 'pix']],
            'with_date_range' => [
                [
                    'data_pagamento_inicio' => '2024-01-01',
                    'data_pagamento_fim' => '2024-01-31',
                ],
            ],
            'with_pagination' => [['page' => 2, 'per_page' => 10]],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'tipo_invalid' => [['tipo' => 'invalido'], 'tipo'],
            'metodo_pagamento_invalid' => [['metodo_pagamento' => 'invalido'], 'metodo_pagamento'],
            'data_pagamento_inicio_invalid' => [['data_pagamento_inicio' => 'invalid'], 'data_pagamento_inicio'],
            'data_pagamento_fim_before_inicio' => [
                [
                    'data_pagamento_inicio' => '2024-01-31',
                    'data_pagamento_fim' => '2024-01-01',
                ],
                'data_pagamento_fim',
            ],
            'page_invalid' => [['page' => 0], 'page'],
            'per_page_too_high' => [['per_page' => 101], 'per_page'],
            'locacao_id_not_exists' => [['locacao_id' => 99999], 'locacao_id'],
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
        $result = PagamentoQueryData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(PagamentoQueryData::class, $result);
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
            PagamentoQueryData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
