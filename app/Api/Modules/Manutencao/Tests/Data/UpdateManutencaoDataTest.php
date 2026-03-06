<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Data;

use App\Api\Modules\Manutencao\Data\UpdateManutencaoData;
use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class UpdateManutencaoDataTest extends TestCase
{
    use RefreshDatabase;

    private function createRequestWithPayload(array $payload): Request
    {
        return Request::create('/api/v1/manutencao/1', 'PUT', $payload);
    }

    public static function validDataProvider(): array
    {
        return [
            'empty_payload' => [[]],
            'only_carro_id' => [['carro_id' => 1]],
            'only_tipo' => [['tipo' => 'preventiva']],
            'only_descricao' => [['descricao' => 'Nova descrição']],
            'only_valor' => [['valor' => 300.00]],
            'only_km_manutencao' => [['km_manutencao' => 60000]],
            'only_data_manutencao' => [['data_manutencao' => '2024-02-20']],
            'only_data_proxima' => [['data_manutencao' => '2024-02-20', 'data_proxima' => '2024-08-20']],
            'only_fornecedor' => [['fornecedor' => 'Oficina ABC']],
            'only_status' => [['status' => 'concluida']],
            'only_observacoes' => [['observacoes' => 'Observação atualizada']],
            'all_fields' => [
                [
                    'carro_id' => 1,
                    'tipo' => 'corretiva',
                    'descricao' => 'Reparo completo',
                    'valor' => 500.00,
                    'km_manutencao' => 75000,
                    'data_manutencao' => '2024-03-01',
                    'data_proxima' => '2024-09-01',
                    'fornecedor' => 'Oficina Premium',
                    'status' => 'em_andamento',
                    'observacoes' => 'Em andamento',
                ],
            ],
            'status_agendada' => [['status' => 'agendada']],
            'status_cancelada' => [['status' => 'cancelada']],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'carro_id_not_exists' => [['carro_id' => 99999], 'carro_id'],
            'tipo_invalid' => [['tipo' => 'invalido'], 'tipo'],
            'descricao_too_long' => [['descricao' => str_repeat('a', 256)], 'descricao'],
            'valor_negative' => [['valor' => -10], 'valor'],
            'km_manutencao_negative' => [['km_manutencao' => -1], 'km_manutencao'],
            'data_manutencao_invalid' => [['data_manutencao' => 'invalid'], 'data_manutencao'],
            'data_proxima_before_data_manutencao' => [
                ['data_manutencao' => '2024-06-01', 'data_proxima' => '2024-01-01'],
                'data_proxima',
            ],
            'status_invalid' => [['status' => 'invalido'], 'status'],
            'fornecedor_too_long' => [['fornecedor' => str_repeat('a', 101)], 'fornecedor'],
            'observacoes_too_long' => [['observacoes' => str_repeat('a', 1001)], 'observacoes'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $carro = $this->createCarro();

        if (isset($validItem['carro_id']) && $validItem['carro_id'] === 1) {
            $validItem['carro_id'] = $carro->id;
        }
        $request = $this->createRequestWithPayload($validItem);

        // Act
        $result = UpdateManutencaoData::from($request);

        // Assert
        $this->assertInstanceOf(UpdateManutencaoData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $carro = $this->createCarro();

        if (isset($invalidItem['carro_id']) && $invalidItem['carro_id'] !== 99999) {
            $invalidItem['carro_id'] = $carro->id;
        }
        $request = $this->createRequestWithPayload($invalidItem);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateManutencaoData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    private function createCarro(): Carro
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        return Carro::factory()->create(['modelo_id' => $modelo->id]);
    }
}
