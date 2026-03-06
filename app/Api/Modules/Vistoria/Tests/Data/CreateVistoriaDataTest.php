<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\Data;

use App\Api\Modules\Vistoria\Data\CreateVistoriaData;
use App\Models\Locacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class CreateVistoriaDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(int $locacaoId): array
    {
        return [
            'locacao_id' => $locacaoId,
            'tipo' => 'retirada',
            'combustivel_nivel' => 'metade',
            'km_registrado' => 50000,
            'observacoes' => null,
            'data_vistoria' => '2024-01-15',
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [[]],
            'with_observacoes' => [['observacoes' => 'Observação de teste']],
            'observacoes_max_length' => [['observacoes' => str_repeat('a', 1000)]],
            'km_zero' => [['km_registrado' => 0]],
            'tipo_devolucao' => [['tipo' => 'devolucao']],
            'combustivel_vazio' => [['combustivel_nivel' => 'vazio']],
            'combustivel_cheio' => [['combustivel_nivel' => 'cheio']],
            'combustivel_1_4' => [['combustivel_nivel' => '1_4']],
            'combustivel_3_4' => [['combustivel_nivel' => '3_4']],
        ];
    }

    public static function invalidDataProvider(): array
    {
        $base = [
            'tipo' => 'retirada',
            'combustivel_nivel' => 'metade',
            'km_registrado' => 50000,
            'data_vistoria' => '2024-01-15',
        ];

        return [
            'locacao_id_missing' => [collect(array_merge($base, ['locacao_id' => 1]))->except('locacao_id')->toArray(), 'locacao_id'],
            'locacao_id_not_exists' => [array_merge($base, ['locacao_id' => 99999]), 'locacao_id'],
            'locacao_id_not_integer' => [array_merge($base, ['locacao_id' => 'abc']), 'locacao_id'],
            'tipo_missing' => [collect(array_merge($base, ['locacao_id' => 1]))->except('tipo')->toArray(), 'tipo'],
            'tipo_invalid' => [array_merge($base, ['locacao_id' => 1, 'tipo' => 'invalido']), 'tipo'],
            'combustivel_nivel_missing' => [collect(array_merge($base, ['locacao_id' => 1]))->except('combustivel_nivel')->toArray(), 'combustivel_nivel'],
            'combustivel_nivel_invalid' => [array_merge($base, ['locacao_id' => 1, 'combustivel_nivel' => 'invalido']), 'combustivel_nivel'],
            'km_registrado_missing' => [collect(array_merge($base, ['locacao_id' => 1]))->except('km_registrado')->toArray(), 'km_registrado'],
            'km_registrado_negative' => [array_merge($base, ['locacao_id' => 1, 'km_registrado' => -1]), 'km_registrado'],
            'km_registrado_not_integer' => [array_merge($base, ['locacao_id' => 1, 'km_registrado' => 1.5]), 'km_registrado'],
            'observacoes_too_long' => [array_merge($base, ['locacao_id' => 1, 'observacoes' => str_repeat('a', 1001)]), 'observacoes'],
            'data_vistoria_missing' => [collect(array_merge($base, ['locacao_id' => 1]))->except('data_vistoria')->toArray(), 'data_vistoria'],
            'data_vistoria_invalid' => [array_merge($base, ['locacao_id' => 1, 'data_vistoria' => 'data-invalida']), 'data_vistoria'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $overrides): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();
        $payload = array_merge(self::validPayload($locacao->id), $overrides);

        // Act
        $result = CreateVistoriaData::validateAndCreate($payload);

        // Assert
        $this->assertInstanceOf(CreateVistoriaData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        if (isset($invalidItem['locacao_id']) && $invalidItem['locacao_id'] === 1) {
            $invalidItem['locacao_id'] = $locacao->id;
        }
        $payload = $invalidItem;

        $this->expectException(ValidationException::class);

        try {
            CreateVistoriaData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
