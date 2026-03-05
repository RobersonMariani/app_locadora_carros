<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Integrations;

use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class DeleteCarroIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnNoContentWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/carro/'.$carro->id)
            ->assertNoContent();
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
            ->deleteJson('/api/v1/carro/99999')
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->deleteJson('/api/v1/carro/1')
            ->assertUnauthorized();
    }
}
