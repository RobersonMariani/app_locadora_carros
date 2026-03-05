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
class FinalizarLocacaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnLocacaoFinalizadaWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $cliente = Cliente::factory()->create();
        $carro = Carro::factory()->create(['disponivel' => false]);
        $locacao = Locacao::factory()->ativa()->create([
            'cliente_id' => $cliente->id,
            'carro_id' => $carro->id,
            'data_inicio_periodo' => '2024-01-10',
            'data_final_previsto_periodo' => '2024-01-15',
            'valor_diaria' => 100,
            'km_inicial' => 50000,
        ]);
        $payload = [
            'km_final' => 50100,
            'data_final_realizado_periodo' => '2024-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/locacao/'.$locacao->id.'/finalizar', $payload)
            ->assertOk()
            ->assertJsonPath('data.status', 'finalizada')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'valor_total',
                    'km_final',
                    'data_final_realizado_periodo',
                ],
            ]);

        $this->assertDatabaseHas('locacoes', [
            'id' => $locacao->id,
            'status' => LocacaoStatusEnum::FINALIZADA->value,
        ]);
        $this->assertDatabaseHas('carros', [
            'id' => $carro->id,
            'disponivel' => true,
        ]);
    }

    public function testShouldReturnUnprocessableWhenRequiredFieldsMissing(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->ativa()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/locacao/'.$locacao->id.'/finalizar', [])
            ->assertUnprocessable();
    }

    public function testShouldReturnNotFoundWhenLocacaoDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'km_final' => 50100,
            'data_final_realizado_periodo' => '2024-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/locacao/99999/finalizar', $payload)
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->ativa()->create();
        $payload = [
            'km_final' => 50100,
            'data_final_realizado_periodo' => '2024-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->patchJson('/api/v1/locacao/'.$locacao->id.'/finalizar', $payload)
            ->assertUnauthorized();
    }
}
