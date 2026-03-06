<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Integrations;

use App\Models\Carro;
use App\Models\Manutencao;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class DeleteManutencaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/manutencao';

    private function createCarro(): Carro
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        return Carro::factory()->create(['modelo_id' => $modelo->id]);
    }

    public function testShouldReturnNoContentWhenManutencaoExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create(['carro_id' => $carro->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson(self::ENDPOINT.'/'.$manutencao->id)
            ->assertNoContent();
    }

    public function testShouldReturnNotFoundWhenManutencaoDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson(self::ENDPOINT.'/99999')
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create(['carro_id' => $carro->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->deleteJson(self::ENDPOINT.'/'.$manutencao->id)
            ->assertUnauthorized();
    }
}
