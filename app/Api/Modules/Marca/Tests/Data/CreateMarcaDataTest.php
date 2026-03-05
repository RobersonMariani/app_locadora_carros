<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Data;

use App\Api\Modules\Marca\Data\CreateMarcaData;
use App\Models\Marca;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class CreateMarcaDataTest extends TestCase
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
            'nome' => 'Toyota',
            'imagem' => UploadedFile::fake()->image('marca.png'),
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'nome_max_length' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 30)])],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'nome_null' => [array_merge(self::validPayload(), ['nome' => null]), 'nome'],
            'nome_empty' => [array_merge(self::validPayload(), ['nome' => '']), 'nome'],
            'nome_too_long' => [array_merge(self::validPayload(), ['nome' => str_repeat('a', 31)]), 'nome'],
            'nome_not_string' => [array_merge(self::validPayload(), ['nome' => 123]), 'nome'],
            'imagem_null' => [array_merge(self::validPayload(), ['imagem' => null]), 'imagem'],
            'imagem_not_file' => [array_merge(self::validPayload(), ['imagem' => 'not-a-file']), 'imagem'],
            'imagem_invalid_mime' => [
                array_merge(self::validPayload(), ['imagem' => UploadedFile::fake()->image('marca.jpg')]),
                'imagem',
            ],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange & Act
        $request = $this->createRequest($validItem);
        $result = CreateMarcaData::from($request);

        // Assert
        $this->assertInstanceOf(CreateMarcaData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            $request = $this->createRequest($invalidItem);
            CreateMarcaData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testShouldFailValidationWhenNomeAlreadyExists(): void
    {
        // Arrange
        Marca::factory()->create(['nome' => 'Toyota']);
        $payload = self::validPayload();

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            $request = $this->createRequest($payload);
            CreateMarcaData::from($request);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nome', $e->errors());

            throw $e;
        }
    }
}
