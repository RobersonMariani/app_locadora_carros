<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Integrations;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class UpdateMarcaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnUpdatedMarcaWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create(['nome' => 'Toyota']);
        $payload = ['nome' => 'Toyota Atualizado'];

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/marca/'.$marca->id, $payload)
            ->assertOk();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('nome', $data);
        $this->assertEquals('Toyota Atualizado', $data['nome']);
    }

    public function testShouldReturnUpdatedMarcaWhenImagemProvided(): void
    {
        // Arrange
        Storage::fake('public');
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $payload = [
            'nome' => 'Honda',
            'imagem' => UploadedFile::fake()->image('marca.png'),
        ];

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->put('/api/v1/marca/'.$marca->id, $payload)
            ->assertOk();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('nome', $data);
        $this->assertArrayHasKey('imagem_url', $data);
    }

    public function testShouldReturn404WhenMarcaNotFound(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = ['nome' => 'Toyota'];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/marca/99999', $payload)
            ->assertNotFound()
            ->assertJson(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe']);
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->putJson('/api/v1/marca/1', ['nome' => 'Toyota'])
            ->assertUnauthorized();
    }
}
