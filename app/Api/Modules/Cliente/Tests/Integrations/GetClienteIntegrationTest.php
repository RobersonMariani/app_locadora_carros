<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Integrations;

use App\Api\Modules\Cliente\Tests\Assertables\ClienteAssertableJson;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class GetClienteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT_INDEX = '/api/v1/cliente';

    public function testIndexShouldReturnPaginatedClientesWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create(['nome' => 'Cliente Index Test']);
        Cliente::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX)
            ->assertOk()
            ->assertJsonFragment(['nome' => 'Cliente Index Test', 'id' => $cliente->id]);
    }

    public function testShowShouldReturnClienteWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/cliente/'.$cliente->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                ClienteAssertableJson::schema($json);
            });
    }

    public function testShowShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/cliente/99999')
            ->assertNotFound();
    }

    public function testIndexShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT_INDEX)
            ->assertUnauthorized();
    }

    public function testShowShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT_INDEX.'/1')
            ->assertUnauthorized();
    }

    public function testIndexShouldReturnClientesFilteredByCidadeEstadoBloqueadoWhenQueryParamsProvided(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $clienteBloqueado = Cliente::factory()->create([
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'bloqueado' => true,
        ]);
        Cliente::factory()->create([
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'bloqueado' => false,
        ]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'?cidade=São Paulo&estado=SP&bloqueado=1')
            ->assertOk();

        $response->assertJsonFragment(['id' => $clienteBloqueado->id, 'cidade' => 'São Paulo', 'estado' => 'SP']);
    }
}
