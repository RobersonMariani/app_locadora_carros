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
class GetPagamentosIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/pagamento';

    public function testShouldReturnPaginatedPagamentosWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        Pagamento::factory()->count(3)->create(['locacao_id' => $locacao->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'locacao_id',
                        'valor',
                        'tipo',
                        'tipo_label',
                        'metodo_pagamento',
                        'metodo_pagamento_label',
                        'data_pagamento',
                        'observacoes',
                        'created_at',
                    ],
                ],
                'links',
                'meta',
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
