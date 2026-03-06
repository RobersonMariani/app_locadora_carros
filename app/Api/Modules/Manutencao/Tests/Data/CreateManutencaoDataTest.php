<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Data;

use App\Api\Modules\Manutencao\Data\CreateManutencaoData;
use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class CreateManutencaoDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'carro_id' => 1,
            'tipo' => 'preventiva',
            'descricao' => 'Troca de óleo',
            'valor' => 250.50,
            'km_manutencao' => 50000,
            'data_manutencao' => '2024-01-15',
            'status' => 'agendada',
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'descricao_max_length' => [array_merge(self::validPayload(), ['descricao' => str_repeat('a', 255)])],
            'km_manutencao_zero' => [array_merge(self::validPayload(), ['km_manutencao' => 0])],
            'valor_zero' => [array_merge(self::validPayload(), ['valor' => 0])],
            'with_data_proxima' => [array_merge(self::validPayload(), ['data_proxima' => '2024-07-15'])],
            'with_fornecedor' => [array_merge(self::validPayload(), ['fornecedor' => 'Oficina XYZ'])],
            'with_observacoes' => [array_merge(self::validPayload(), ['observacoes' => 'Observação importante'])],
            'tipo_corretiva' => [array_merge(self::validPayload(), ['tipo' => 'corretiva'])],
            'tipo_revisao' => [array_merge(self::validPayload(), ['tipo' => 'revisao'])],
            'status_em_andamento' => [array_merge(self::validPayload(), ['status' => 'em_andamento'])],
            'status_concluida' => [array_merge(self::validPayload(), ['status' => 'concluida'])],
            'status_cancelada' => [array_merge(self::validPayload(), ['status' => 'cancelada'])],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'carro_id_missing' => [collect(self::validPayload())->except('carro_id')->toArray(), 'carro_id'],
            'carro_id_not_exists' => [array_merge(self::validPayload(), ['carro_id' => 99999]), 'carro_id'],
            'tipo_missing' => [collect(self::validPayload())->except('tipo')->toArray(), 'tipo'],
            'tipo_invalid' => [array_merge(self::validPayload(), ['tipo' => 'invalido']), 'tipo'],
            'tipo_empty' => [array_merge(self::validPayload(), ['tipo' => '']), 'tipo'],
            'descricao_missing' => [collect(self::validPayload())->except('descricao')->toArray(), 'descricao'],
            'descricao_empty' => [array_merge(self::validPayload(), ['descricao' => '']), 'descricao'],
            'descricao_too_long' => [array_merge(self::validPayload(), ['descricao' => str_repeat('a', 256)]), 'descricao'],
            'valor_missing' => [collect(self::validPayload())->except('valor')->toArray(), 'valor'],
            'valor_negative' => [array_merge(self::validPayload(), ['valor' => -10]), 'valor'],
            'valor_not_numeric' => [array_merge(self::validPayload(), ['valor' => 'abc']), 'valor'],
            'km_manutencao_missing' => [collect(self::validPayload())->except('km_manutencao')->toArray(), 'km_manutencao'],
            'km_manutencao_negative' => [array_merge(self::validPayload(), ['km_manutencao' => -1]), 'km_manutencao'],
            'data_manutencao_missing' => [collect(self::validPayload())->except('data_manutencao')->toArray(), 'data_manutencao'],
            'data_manutencao_invalid' => [array_merge(self::validPayload(), ['data_manutencao' => 'invalid']), 'data_manutencao'],
            'data_proxima_before_data_manutencao' => [
                array_merge(self::validPayload(), ['data_proxima' => '2024-01-01']),
                'data_proxima',
            ],
            'status_missing' => [collect(self::validPayload())->except('status')->toArray(), 'status'],
            'status_invalid' => [array_merge(self::validPayload(), ['status' => 'invalido']), 'status'],
            'fornecedor_too_long' => [array_merge(self::validPayload(), ['fornecedor' => str_repeat('a', 101)]), 'fornecedor'],
            'observacoes_too_long' => [array_merge(self::validPayload(), ['observacoes' => str_repeat('a', 1001)]), 'observacoes'],
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

        // Act
        $result = CreateManutencaoData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CreateManutencaoData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $carro = $this->createCarro();

        if (isset($invalidItem['carro_id']) && $invalidItem['carro_id'] === 1) {
            $invalidItem['carro_id'] = $carro->id;
        }

        if (isset($invalidItem['carro_id']) && $invalidItem['carro_id'] !== 99999) {
            $invalidItem['carro_id'] = $carro->id;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateManutencaoData::validateAndCreate($invalidItem);
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
