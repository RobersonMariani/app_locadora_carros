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
class DeletePagamentoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnNoContentWhenPagamentoExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        $pagamento = Pagamento::factory()->create(['locacao_id' => $locacao->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/pagamento/'.$pagamento->id)
            ->assertNoContent();
    }

    public function testShouldReturnNotFoundWhenPagamentoDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/pagamento/99999')
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();
        $pagamento = Pagamento::factory()->create(['locacao_id' => $locacao->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->deleteJson('/api/v1/pagamento/'.$pagamento->id)
            ->assertUnauthorized();
    }
}
