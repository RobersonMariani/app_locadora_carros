<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Data;

use App\Api\Modules\Carro\Data\UpdateCarroData;
use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class UpdateCarroDataTest extends TestCase
{
    use RefreshDatabase;

    private function createRequestWithRoute(int $carroId, array $payload): Request
    {
        $request = Request::create('/api/v1/carro/'.$carroId, 'PUT', $payload);

        $route = new Route('PUT', 'api/v1/carro/{carro}', []);
        $route->bind($request);
        $route->setParameter('carro', (string) $carroId);
        $request->setRouteResolver(fn () => $route);

        $this->app->instance('request', $request);

        return $request;
    }

    public static function validDataProvider(): array
    {
        return [
            'only_modelo_id' => [['modelo_id' => 1]],
            'only_placa' => [['placa' => 'ABC1234']],
            'only_disponivel' => [['disponivel' => false]],
            'only_km' => [['km' => 100000]],
            'all_fields' => [
                [
                    'modelo_id' => 1,
                    'placa' => 'XYZ9876',
                    'disponivel' => true,
                    'km' => 75000,
                    'combustivel' => 'flex',
                    'cambio' => 'automatico',
                    'categoria' => 'sedan',
                    'ar_condicionado' => false,
                    'diaria_padrao' => 180.00,
                ],
            ],
            'placa_max_length' => [['placa' => str_repeat('A', 10)]],
            'only_combustivel' => [['combustivel' => 'gasolina']],
            'only_cambio' => [['cambio' => 'manual']],
            'only_categoria' => [['categoria' => 'suv']],
            'only_ar_condicionado' => [['ar_condicionado' => false]],
            'only_diaria_padrao' => [['diaria_padrao' => 250.50]],
            'empty_payload' => [[]],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'modelo_id_not_exists' => [['modelo_id' => 99999], 'modelo_id'],
            'placa_too_long' => [['placa' => str_repeat('A', 11)], 'placa'],
            'placa_not_string' => [['placa' => 123], 'placa'],
            'disponivel_not_boolean' => [['disponivel' => 'yes'], 'disponivel'],
            'km_not_integer' => [['km' => 1.5], 'km'],
            'combustivel_invalid' => [['combustivel' => 'invalido'], 'combustivel'],
            'cambio_invalid' => [['cambio' => 'invalido'], 'cambio'],
            'categoria_invalid' => [['categoria' => 'invalido'], 'categoria'],
            'diaria_padrao_negative' => [['diaria_padrao' => -50], 'diaria_padrao'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);

        if (isset($validItem['modelo_id']) && $validItem['modelo_id'] === 1) {
            $validItem['modelo_id'] = $modelo->id;
        }

        $request = $this->createRequestWithRoute($carro->id, $validItem);

        // Act
        $result = UpdateCarroData::from($request);

        // Assert
        $this->assertInstanceOf(UpdateCarroData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);

        if (isset($invalidItem['modelo_id']) && $invalidItem['modelo_id'] !== 99999) {
            $invalidItem['modelo_id'] = $modelo->id;
        }

        $request = $this->createRequestWithRoute($carro->id, $invalidItem);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateCarroData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testShouldFailValidationWhenPlacaAlreadyExistsForOtherCarro(): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        Carro::factory()->create([
            'modelo_id' => $modelo->id,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 10000,
        ]);
        $carro = Carro::factory()->create([
            'modelo_id' => $modelo->id,
            'placa' => 'XYZ9876',
            'disponivel' => true,
            'km' => 20000,
        ]);
        $request = $this->createRequestWithRoute($carro->id, ['placa' => 'ABC1234']);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateCarroData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('placa', $e->errors());

            throw $e;
        }
    }

    public function testShouldPassValidationWhenPlacaSameAsCurrentCarro(): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create([
            'modelo_id' => $modelo->id,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 10000,
        ]);
        $request = $this->createRequestWithRoute($carro->id, ['placa' => 'ABC1234']);

        // Act
        $result = UpdateCarroData::from($request);

        // Assert
        $this->assertInstanceOf(UpdateCarroData::class, $result);
    }
}
