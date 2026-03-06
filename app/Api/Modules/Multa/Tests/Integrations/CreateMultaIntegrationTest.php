<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Integrations;

use App\Api\Modules\Multa\Tests\Assertables\MultaAssertableJson;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class CreateMultaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/multa';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        $payload = [
            'locacao_id' => $locacao->id,
            'carro_id' => $locacao->carro_id,
            'cliente_id' => $locacao->cliente_id,
            'valor' => 150.50,
            'data_infracao' => '2024-01-15',
            'descricao' => 'Excesso de velocidade',
            'status' => 'pendente',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                MultaAssertableJson::schema($json);
            });
    }

    public function testShouldReturnCreatedWhenDataIsValidWithOptionalFields(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        $payload = [
            'locacao_id' => $locacao->id,
            'carro_id' => $locacao->carro_id,
            'cliente_id' => $locacao->cliente_id,
            'valor' => 200.00,
            'data_infracao' => '2024-02-20',
            'descricao' => 'Estacionamento proibido',
            'status' => 'paga',
            'codigo_infracao' => '12345',
            'pontos' => 5,
            'data_pagamento' => '2024-03-01',
            'observacoes' => 'Multa paga em dia',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                MultaAssertableJson::schema($json)
                    ->where('valor', 200)
                    ->where('status', 'paga')
                    ->where('codigo_infracao', '12345')
                    ->where('pontos', 5)
                    ->etc();
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

    public function testShouldReturnUnprocessableWhenInvalidFields(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'locacao_id' => 99999,
            'carro_id' => 99999,
            'cliente_id' => 99999,
            'valor' => -10,
            'data_infracao' => 'invalid',
            'descricao' => '',
            'status' => 'invalido',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertUnprocessable();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = $this->createLocacaoWithRelations();
        $payload = [
            'locacao_id' => $locacao->id,
            'carro_id' => $locacao->carro_id,
            'cliente_id' => $locacao->cliente_id,
            'valor' => 150.50,
            'data_infracao' => '2024-01-15',
            'descricao' => 'Excesso de velocidade',
            'status' => 'pendente',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }

    private function createLocacaoWithRelations(): Locacao
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);
        $cliente = Cliente::factory()->create();

        return Locacao::factory()->create([
            'carro_id' => $carro->id,
            'cliente_id' => $cliente->id,
        ]);
    }
}
