<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Integrations;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class IniciarLocacaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnLocacaoAtivaWhenIniciarSucceeds(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create();
        $carro = Carro::factory()->create(['disponivel' => true]);
        $locacao = Locacao::factory()->create([
            'cliente_id' => $cliente->id,
            'carro_id' => $carro->id,
            'status' => LocacaoStatusEnum::RESERVADA,
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/locacao/'.$locacao->id.'/iniciar')
            ->assertOk()
            ->assertJsonPath('data.status', 'ativa');

        $this->assertDatabaseHas('locacoes', [
            'id' => $locacao->id,
            'status' => LocacaoStatusEnum::ATIVA->value,
        ]);
        $this->assertDatabaseHas('carros', [
            'id' => $carro->id,
            'disponivel' => false,
        ]);
    }

    public function testShouldReturnNotFoundWhenLocacaoDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/locacao/99999/iniciar')
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->patchJson('/api/v1/locacao/'.$locacao->id.'/iniciar')
            ->assertUnauthorized();
    }
}
