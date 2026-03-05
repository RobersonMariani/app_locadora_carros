<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\Data;

use App\Api\Modules\Modelo\Data\UpdateModeloData;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class UpdateModeloDataTest extends TestCase
{
    use RefreshDatabase;

    private function createRequestWithRoute(int $modeloId, array $payload): Request
    {
        $request = Request::create('/api/v1/modelo/'.$modeloId, 'PUT', $payload);

        if (isset($payload['imagem']) && $payload['imagem'] instanceof UploadedFile) {
            $request->files->set('imagem', $payload['imagem']);
        }

        $route = new Route('PUT', 'api/v1/modelo/{modelo}', []);
        $route->bind($request);
        $route->setParameter('modelo', (string) $modeloId);
        $request->setRouteResolver(fn () => $route);

        $this->app->instance('request', $request);

        return $request;
    }

    public static function validDataProvider(): array
    {
        return [
            'empty_payload' => [[]],
            'only_nome' => [['nome' => 'Corolla Atualizado']],
            'only_marca_id' => [['marca_id' => 1]],
            'only_imagem' => [['imagem' => UploadedFile::fake()->image('modelo.png')]],
            'only_numero_portas' => [['numero_portas' => 5]],
            'only_lugares' => [['lugares' => 7]],
            'only_air_bag' => [['air_bag' => false]],
            'only_abs' => [['abs' => false]],
            'nome_max_length' => [['nome' => str_repeat('a', 30)]],
            'nome_min_length' => [['nome' => 'ABC']],
            'multiple_fields' => [
                [
                    'nome' => 'Hilux',
                    'numero_portas' => 4,
                    'lugares' => 5,
                    'air_bag' => true,
                    'abs' => true,
                ],
            ],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'nome_too_long' => [['nome' => str_repeat('a', 31)], 'nome'],
            'nome_too_short' => [['nome' => 'AB'], 'nome'],
            'nome_not_string' => [['nome' => 123], 'nome'],
            'marca_id_not_exists' => [['marca_id' => 99999], 'marca_id'],
            'imagem_invalid_mime' => [
                ['imagem' => UploadedFile::fake()->create('modelo.pdf', 100)],
                'imagem',
            ],
            'air_bag_not_boolean' => [['air_bag' => 'yes'], 'air_bag'],
            'abs_not_boolean' => [['abs' => 'no'], 'abs'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $payload = $validItem;

        if (isset($validItem['marca_id']) && $validItem['marca_id'] === 1) {
            $payload['marca_id'] = $marca->id;
        }
        $request = $this->createRequestWithRoute($modelo->id, $payload);

        // Act
        $result = UpdateModeloData::from($request);

        // Assert
        $this->assertInstanceOf(UpdateModeloData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $payload = $invalidItem;

        if (isset($invalidItem['marca_id']) && $invalidItem['marca_id'] === 99999) {
            $payload['marca_id'] = 99999;
        }
        $request = $this->createRequestWithRoute($modelo->id, $payload);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateModeloData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testShouldFailValidationWhenNomeAlreadyExistsForOtherModelo(): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        Modelo::factory()->create(['marca_id' => $marca->id, 'nome' => 'Corolla']);
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id, 'nome' => 'Hilux']);
        $request = $this->createRequestWithRoute($modelo->id, ['nome' => 'Corolla']);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateModeloData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nome', $e->errors());

            throw $e;
        }
    }
}
