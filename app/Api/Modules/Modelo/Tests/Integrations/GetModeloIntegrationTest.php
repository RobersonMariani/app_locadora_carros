<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\Integrations;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class GetModeloIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/modelo';

    public function testIndexShouldReturnPaginatedModelosWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        Modelo::factory()->count(3)->create(['marca_id' => $marca->id]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT)
            ->assertOk();

        $json = $response->json();
        $data = $json['data'] ?? $json;
        $this->assertIsArray($data);

        if (count($data) > 0) {
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('marca_id', $data[0]);
            $this->assertArrayHasKey('nome', $data[0]);
            $this->assertArrayHasKey('imagem_url', $data[0]);
            $this->assertArrayHasKey('numero_portas', $data[0]);
            $this->assertArrayHasKey('lugares', $data[0]);
            $this->assertArrayHasKey('air_bag', $data[0]);
            $this->assertArrayHasKey('abs', $data[0]);
        }
    }

    public function testShowShouldReturnModeloWhenFound(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT.'/'.$modelo->id)
            ->assertOk();

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

    public function testShowShouldReturn404WhenModeloNotFound(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT.'/99999')
            ->assertNotFound()
            ->assertJson(['erro' => 'Modelo pesquisado não existe']);
    }

    public function testIndexShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT)
            ->assertUnauthorized();
    }

    public function testShowShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT.'/1')
            ->assertUnauthorized();
    }
}
