<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Integrations;

use App\Models\Locacao;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class GetPagamentosByLocacaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnPagamentosWhenLocacaoExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        Pagamento::factory()->count(2)->create(['locacao_id' => $locacao->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/locacao/'.$locacao->id.'/pagamento')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testShouldReturnEmptyArrayWhenLocacaoHasNoPagamentos(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/locacao/'.$locacao->id.'/pagamento')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/v1/locacao/'.$locacao->id.'/pagamento')
            ->assertUnauthorized();
    }
}
