<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Integrations;

use App\Api\Modules\Multa\Tests\Assertables\MultaAssertableJson;
use App\Models\Multa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class UpdateMultaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/multa';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function testShouldReturnUpdatedMultaWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multa = Multa::factory()->create();
        $payload = [
            'valor' => 250.00,
            'descricao' => 'Descrição atualizada',
            'status' => 'paga',
            'data_pagamento' => '2024-03-15',
            'observacoes' => 'Multa paga',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/'.$multa->id, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                MultaAssertableJson::schema($json);
            });
    }

    public function testShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'valor' => 250.00,
            'descricao' => 'Descrição atualizada',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/99999', $payload)
            ->assertNotFound();
    }

    public function testShouldReturnUnprocessableWhenInvalidFields(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multa = Multa::factory()->create();
        $payload = [
            'valor' => -50,
            'status' => 'invalido',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/'.$multa->id, $payload)
            ->assertUnprocessable();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->putJson(self::ENDPOINT.'/1', ['valor' => 200.00])
            ->assertUnauthorized();
    }
}
