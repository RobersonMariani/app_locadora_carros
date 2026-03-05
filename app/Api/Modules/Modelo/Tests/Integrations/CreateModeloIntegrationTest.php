<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\Integrations;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class CreateModeloIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/modelo';

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        Storage::fake('public');
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $payload = [
            'marca_id' => $marca->id,
            'nome' => 'Corolla',
            'imagem' => UploadedFile::fake()->image('modelo.png'),
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ];

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertCreated();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('marca_id', $data);
        $this->assertArrayHasKey('nome', $data);
        $this->assertArrayHasKey('imagem_url', $data);
        $this->assertArrayHasKey('numero_portas', $data);
        $this->assertArrayHasKey('lugares', $data);
        $this->assertArrayHasKey('air_bag', $data);
        $this->assertArrayHasKey('abs', $data);
    }

    public function testShouldReturnUnprocessableWhenRequiredFieldsMissing(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, [])
            ->assertUnprocessable();
    }

    public function testShouldReturnUnprocessableWhenImagemInvalidMime(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $payload = [
            'marca_id' => $marca->id,
            'nome' => 'Corolla',
            'imagem' => UploadedFile::fake()->create('modelo.pdf', 100),
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertUnprocessable();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $marca = Marca::factory()->create();
        $payload = [
            'marca_id' => $marca->id,
            'nome' => 'Corolla',
            'imagem' => UploadedFile::fake()->image('modelo.png'),
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }
}
