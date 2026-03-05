<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Integrations;

use App\Api\Modules\Pagamento\Tests\Assertables\PagamentoAssertableJson;
use App\Models\Locacao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class CreatePagamentoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/pagamento';

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        $payload = [
            'locacao_id' => $locacao->id,
            'valor' => 100.50,
            'tipo' => 'diaria',
            'metodo_pagamento' => 'pix',
            'data_pagamento' => '2024-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    PagamentoAssertableJson::schema($json);
                })->etc();
            });
    }

    public function testShouldReturnUnprocessableWhenRequiredFieldsMissing(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, [])
            ->assertUnprocessable();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();
        $payload = [
            'locacao_id' => $locacao->id,
            'valor' => 100.50,
            'tipo' => 'diaria',
            'metodo_pagamento' => 'pix',
            'data_pagamento' => '2024-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }
}
