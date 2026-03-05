<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Tests\Integrations;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('dashboard')]
class GetFaturamentoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/dashboard/faturamento';

    public function testShouldReturnFaturamentoMensalWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT.'?periodo=mensal')
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'periodo',
                    'faturamento',
                    'quantidade_locacoes',
                ],
            ]);
    }

    public function testShouldReturnFaturamentoSemanalWhenPeriodoIsSemanal(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT.'?periodo=semanal')
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'periodo',
                    'faturamento',
                    'quantidade_locacoes',
                ],
            ]);
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT)
            ->assertUnauthorized();
    }
}
