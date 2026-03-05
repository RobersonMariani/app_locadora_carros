<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\Data;

use App\Api\Modules\Modelo\Data\CreateModeloData;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class CreateModeloDataTest extends TestCase
{
    use RefreshDatabase;

    private function createRequest(array $payload): Request
    {
        $request = Request::create('/', 'POST', $payload);

        if (isset($payload['imagem']) && $payload['imagem'] instanceof UploadedFile) {
            $request->files->set('imagem', $payload['imagem']);
        }
        $this->app->instance('request', $request);

        return $request;
    }

    private static function validPayload(): array
    {
        return [
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => UploadedFile::fake()->image('modelo.png'),
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'nome_min_length' => [array_merge(self::validPayload(), ['nome' => 'ABC'])],
            'nome_max_length' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 30)])],
            'numero_portas_min' => [array_merge(self::validPayload(), ['numero_portas' => 1])],
            'numero_portas_max' => [array_merge(self::validPayload(), ['numero_portas' => 5])],
            'lugares_min' => [array_merge(self::validPayload(), ['lugares' => 1])],
            'lugares_max' => [array_merge(self::validPayload(), ['lugares' => 20])],
            'air_bag_false' => [array_merge(self::validPayload(), ['air_bag' => false])],
            'abs_false' => [array_merge(self::validPayload(), ['abs' => false])],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'marca_id_null' => [array_merge(self::validPayload(), ['marca_id' => null]), 'marca_id'],
            'marca_id_not_exists' => [array_merge(self::validPayload(), ['marca_id' => 99999]), 'marca_id'],
            'marca_id_not_integer' => [array_merge(self::validPayload(), ['marca_id' => 'abc']), 'marca_id'],
            'nome_null' => [array_merge(self::validPayload(), ['nome' => null]), 'nome'],
            'nome_empty' => [array_merge(self::validPayload(), ['nome' => '']), 'nome'],
            'nome_too_short' => [array_merge(self::validPayload(), ['nome' => 'AB']), 'nome'],
            'nome_too_long' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 31)]), 'nome'],
            'nome_not_string' => [array_merge(self::validPayload(), ['nome' => 123]), 'nome'],
            'imagem_null' => [array_merge(self::validPayload(), ['imagem' => null]), 'imagem'],
            'imagem_not_file' => [array_merge(self::validPayload(), ['imagem' => 'not-a-file']), 'imagem'],
            'imagem_invalid_mime' => [
                array_merge(self::validPayload(), ['imagem' => UploadedFile::fake()->create('modelo.pdf', 100)]),
                'imagem',
            ],
            'air_bag_null' => [array_merge(self::validPayload(), ['air_bag' => null]), 'air_bag'],
            'air_bag_not_boolean' => [array_merge(self::validPayload(), ['air_bag' => 'yes']), 'air_bag'],
            'abs_null' => [array_merge(self::validPayload(), ['abs' => null]), 'abs'],
            'abs_not_boolean' => [array_merge(self::validPayload(), ['abs' => 'no']), 'abs'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $payload = array_merge($validItem, ['marca_id' => $marca->id]);
        $request = $this->createRequest($payload);

        // Act
        $result = CreateModeloData::from($request);

        // Assert
        $this->assertInstanceOf(CreateModeloData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $payload = $invalidItem;

        if ($expectedField !== 'marca_id') {
            $payload['marca_id'] = $payload['marca_id'] ?? $marca->id;
        }
        $request = $this->createRequest($payload);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateModeloData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testShouldFailValidationWhenNomeAlreadyExists(): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        Modelo::factory()->create(['marca_id' => $marca->id, 'nome' => 'Corolla']);
        $payload = array_merge(self::validPayload(), ['marca_id' => $marca->id]);
        $request = $this->createRequest($payload);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateModeloData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nome', $e->errors());

            throw $e;
        }
    }
}
