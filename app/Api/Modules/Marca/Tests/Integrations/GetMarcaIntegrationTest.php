<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Integrations;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class GetMarcaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexShouldReturnPaginatedMarcasWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        Marca::factory()->count(3)->create();

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/marca')
            ->assertOk();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertIsArray($data);

        if (count($data) > 0) {
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('nome', $data[0]);
            $this->assertArrayHasKey('imagem_url', $data[0]);
        }
    }

    public function testShowShouldReturnMarcaWhenFound(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/marca/'.$marca->id)
            ->assertOk();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('nome', $data);
        $this->assertArrayHasKey('imagem_url', $data);
    }

    public function testShowShouldReturn404WhenMarcaNotFound(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/marca/99999')
            ->assertNotFound()
            ->assertJson(['erro' => 'Marca pesquisada não existe']);
    }

    public function testIndexShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/v1/marca')
            ->assertUnauthorized();
    }

    public function testShowShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/v1/marca/1')
            ->assertUnauthorized();
    }
}
