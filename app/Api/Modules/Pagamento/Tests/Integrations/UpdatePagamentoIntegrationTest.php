<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Integrations;

use App\Api\Modules\Pagamento\Tests\Assertables\PagamentoAssertableJson;
use App\Models\Locacao;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class UpdatePagamentoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnUpdatedPagamentoWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        $pagamento = Pagamento::factory()->create(['locacao_id' => $locacao->id]);
        $payload = [
            'valor' => 250.00,
            'observacoes' => 'Atualizado',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/pagamento/'.$pagamento->id, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    PagamentoAssertableJson::schema($json);
                })->etc();
            })
            ->assertJsonPath('data.valor', 250)
            ->assertJsonPath('data.observacoes', 'Atualizado');
    }

    public function testShouldReturnNotFoundWhenPagamentoDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = ['valor' => 250.00];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/pagamento/99999', $payload)
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
            ->putJson('/api/v1/pagamento/'.$pagamento->id, ['valor' => 250.00])
            ->assertUnauthorized();
    }
}
