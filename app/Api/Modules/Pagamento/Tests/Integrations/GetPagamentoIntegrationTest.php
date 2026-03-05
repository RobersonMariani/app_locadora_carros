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
class GetPagamentoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnPagamentoWhenFound(): void
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
            ->getJson('/api/v1/pagamento/'.$pagamento->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    PagamentoAssertableJson::schema($json);
                })->etc();
            });
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
            ->getJson('/api/v1/pagamento/99999')
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/v1/pagamento/1')
            ->assertUnauthorized();
    }
}
