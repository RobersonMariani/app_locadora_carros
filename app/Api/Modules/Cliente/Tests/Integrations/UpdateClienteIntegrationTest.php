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
class UpdateClienteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnUpdatedClienteWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create();
        $payload = ['nome' => 'Nome Atualizado'];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/cliente/'.$cliente->id, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                ClienteAssertableJson::schema($json);
            });
    }

    public function testShouldReturnUpdatedClienteWithBloqueioWhenPayloadValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create(['bloqueado' => false]);
        $payload = [
            'bloqueado' => true,
            'motivo_bloqueio' => 'Inadimplência recorrente',
        ];

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/cliente/'.$cliente->id, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                ClienteAssertableJson::schema($json);
            });

        $response->assertJsonFragment([
            'bloqueado' => true,
            'motivo_bloqueio' => 'Inadimplência recorrente',
        ]);
    }

    public function testShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = ['nome' => 'Nome Atualizado'];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/cliente/99999', $payload)
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->putJson('/api/v1/cliente/1', ['nome' => 'Test'])
            ->assertUnauthorized();
    }
}
