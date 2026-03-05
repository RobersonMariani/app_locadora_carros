<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Integrations;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class DeleteMarcaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnSuccessWhenMarcaExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/marca/'.$marca->id)
            ->assertOk()
            ->assertJson(['msg' => 'A marca foi removida com sucesso']);

        $this->assertDatabaseMissing('marcas', ['id' => $marca->id]);
    }

    public function testShouldReturn404WhenMarcaNotFound(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/marca/99999')
            ->assertNotFound()
            ->assertJson(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe']);
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->deleteJson('/api/v1/marca/1')
            ->assertUnauthorized();
    }
}
