<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\Integrations;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class UpdateModeloIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/modelo';

    public function testShouldReturnUpdatedModeloWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $payload = [
            'nome' => 'Corolla Atualizado',
            'numero_portas' => 5,
            'lugares' => 7,
        ];

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/'.$modelo->id, $payload)
            ->assertOk();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertArrayHasKey('nome', $data);
        $this->assertEquals('Corolla Atualizado', $data['nome']);
    }

    public function testShouldReturnUpdatedModeloWhenImagemProvided(): void
    {
        // Arrange
        Storage::fake('public');
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $payload = [
            '_method' => 'PUT',
            'imagem' => UploadedFile::fake()->image('modelo.png'),
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT.'/'.$modelo->id, $payload)
            ->assertOk();
    }

    public function testShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'nome' => 'Corolla Atualizado',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/99999', $payload)
            ->assertNotFound()
            ->assertJson(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe']);
    }
}
