<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Data;

use App\Api\Modules\Carro\Data\CreateCarroData;
use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CreateCarroDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'modelo_id' => 1,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 50000,
            'cor' => 'Branco',
            'ano_fabricacao' => 2023,
            'ano_modelo' => 2024,
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'placa_max_length' => [array_merge(self::validPayload(), ['placa' => str_repeat('A', 10)])],
            'disponivel_false' => [array_merge(self::validPayload(), ['disponivel' => false])],
            'km_zero' => [array_merge(self::validPayload(), ['km' => 0])],
            'with_renavam' => [array_merge(self::validPayload(), ['renavam' => '12345678901'])],
            'with_combustivel' => [array_merge(self::validPayload(), ['combustivel' => 'flex'])],
            'with_cambio' => [array_merge(self::validPayload(), ['cambio' => 'automatico'])],
            'with_categoria' => [array_merge(self::validPayload(), ['categoria' => 'sedan'])],
            'with_ar_condicionado_false' => [array_merge(self::validPayload(), ['ar_condicionado' => false])],
            'with_diaria_padrao' => [array_merge(self::validPayload(), ['diaria_padrao' => 150.50])],
            'with_all_optional_fields' => [array_merge(self::validPayload(), [
                'combustivel' => 'gasolina',
                'cambio' => 'manual',
                'categoria' => 'economico',
                'ar_condicionado' => true,
                'diaria_padrao' => 200.00,
            ])],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'modelo_id_missing' => [collect(self::validPayload())->except('modelo_id')->toArray(), 'modelo_id'],
            'modelo_id_not_exists' => [array_merge(self::validPayload(), ['modelo_id' => 99999]), 'modelo_id'],
            'modelo_id_not_integer' => [array_merge(self::validPayload(), ['modelo_id' => 'abc']), 'modelo_id'],
            'placa_missing' => [collect(self::validPayload())->except('placa')->toArray(), 'placa'],
            'placa_empty' => [array_merge(self::validPayload(), ['placa' => '']), 'placa'],
            'placa_too_long' => [array_merge(self::validPayload(), ['placa' => str_repeat('A', 11)]), 'placa'],
            'disponivel_missing' => [collect(self::validPayload())->except('disponivel')->toArray(), 'disponivel'],
            'disponivel_not_boolean' => [array_merge(self::validPayload(), ['disponivel' => 'yes']), 'disponivel'],
            'km_missing' => [collect(self::validPayload())->except('km')->toArray(), 'km'],
            'km_not_integer' => [array_merge(self::validPayload(), ['km' => 1.5]), 'km'],
            'cor_missing' => [collect(self::validPayload())->except('cor')->toArray(), 'cor'],
            'cor_empty' => [array_merge(self::validPayload(), ['cor' => '']), 'cor'],
            'ano_fabricacao_missing' => [collect(self::validPayload())->except('ano_fabricacao')->toArray(), 'ano_fabricacao'],
            'ano_modelo_missing' => [collect(self::validPayload())->except('ano_modelo')->toArray(), 'ano_modelo'],
            'combustivel_invalid' => [array_merge(self::validPayload(), ['combustivel' => 'invalido']), 'combustivel'],
            'cambio_invalid' => [array_merge(self::validPayload(), ['cambio' => 'invalido']), 'cambio'],
            'categoria_invalid' => [array_merge(self::validPayload(), ['categoria' => 'invalido']), 'categoria'],
            'diaria_padrao_negative' => [array_merge(self::validPayload(), ['diaria_padrao' => -10]), 'diaria_padrao'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        if (isset($validItem['modelo_id']) && $validItem['modelo_id'] === 1) {
            $validItem['modelo_id'] = $modelo->id;
        }

        // Act
        $result = CreateCarroData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CreateCarroData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        if (isset($invalidItem['modelo_id']) && $invalidItem['modelo_id'] === 1) {
            $invalidItem['modelo_id'] = $modelo->id;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateCarroData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testShouldFailValidationWhenPlacaAlreadyExists(): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        Carro::factory()->create(['modelo_id' => $modelo->id, 'placa' => 'ABC1234']);

        $payload = array_merge(self::validPayload(), [
            'modelo_id' => $modelo->id,
            'placa' => 'ABC1234',
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateCarroData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('placa', $e->errors());

            throw $e;
        }
    }
}
