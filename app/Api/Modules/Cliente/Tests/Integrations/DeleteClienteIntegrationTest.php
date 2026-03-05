<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Integrations;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class DeleteClienteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnSuccessWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/cliente/'.$cliente->id)
            ->assertOk()
            ->assertJson(['msg' => 'O cliente foi removido com sucesso']);
    }

    public function testShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/cliente/99999')
            ->assertNotFound();
    }
}
