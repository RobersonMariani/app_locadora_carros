<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Data;

use App\Api\Modules\Marca\Data\UpdateMarcaData;
use App\Models\Marca;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class UpdateMarcaDataTest extends TestCase
{
    use RefreshDatabase;

    private function createRequestWithRoute(int $marcaId, array $payload): Request
    {
        $request = Request::create('/api/v1/marca/'.$marcaId, 'PUT', $payload);

        if (isset($payload['imagem']) && $payload['imagem'] instanceof UploadedFile) {
            $request->files->set('imagem', $payload['imagem']);
        }

        $route = new Route('PUT', 'api/v1/marca/{marca}', []);
        $route->bind($request);
        $route->setParameter('marca', (string) $marcaId);
        $request->setRouteResolver(fn () => $route);

        $this->app->instance('request', $request);

        return $request;
    }

    public static function validDataProvider(): array
    {
        return [
            'only_nome' => [['nome' => 'Toyota Atualizado']],
            'only_imagem' => [['imagem' => UploadedFile::fake()->image('marca.png')]],
            'nome_and_imagem' => [
                [
                    'nome' => 'Honda',
                    'imagem' => UploadedFile::fake()->image('marca.png'),
                ],
            ],
            'nome_max_length' => [['nome' => str_repeat('a', 30)]],
            'empty_payload' => [[]],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'nome_too_long' => [['nome' => str_repeat('a', 31)], 'nome'],
            'nome_not_string' => [['nome' => 123], 'nome'],
            'imagem_invalid_mime' => [
                ['imagem' => UploadedFile::fake()->image('marca.jpg')],
                'imagem',
            ],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $request = $this->createRequestWithRoute($marca->id, $validItem);

        // Act
        $result = UpdateMarcaData::from($request);

        // Assert
        $this->assertInstanceOf(UpdateMarcaData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $request = $this->createRequestWithRoute($marca->id, $invalidItem);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateMarcaData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testShouldFailValidationWhenNomeAlreadyExistsForOtherMarca(): void
    {
        // Arrange
        Marca::factory()->create(['nome' => 'Toyota']);
        $marca = Marca::factory()->create(['nome' => 'Honda']);
        $request = $this->createRequestWithRoute($marca->id, ['nome' => 'Toyota']);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateMarcaData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nome', $e->errors());

            throw $e;
        }
    }

    public function testShouldPassValidationWhenNomeSameAsCurrentMarca(): void
    {
        // Arrange
        $marca = Marca::factory()->create(['nome' => 'Toyota']);
        $request = $this->createRequestWithRoute($marca->id, ['nome' => 'Toyota']);

        // Act
        $result = UpdateMarcaData::from($request);

        // Assert
        $this->assertInstanceOf(UpdateMarcaData::class, $result);
    }
}
