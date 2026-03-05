<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Integrations;

use App\Api\Modules\Locacao\Tests\Assertables\LocacaoAssertableJson;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class CreateLocacaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/locacao';

    private function createClienteAndCarro(): array
    {
        $cliente = Cliente::factory()->create();
        $marca = Marca::factory()->create();
        $modelo = Modelo::create([
            'marca_id' => $marca->id,
            'nome' => 'Modelo Test',
            'imagem' => 'imagem.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);
        $carro = Carro::create([
            'modelo_id' => $modelo->id,
            'placa' => 'ABC-'.fake()->unique()->numerify('####'),
            'disponivel' => true,
            'km' => 0,
            'cor' => 'Branco',
            'ano_fabricacao' => 2023,
            'ano_modelo' => 2024,
        ]);

        return ['cliente' => $cliente, 'carro' => $carro];
    }

    private function validPayload(Cliente $cliente, Carro $carro): array
    {
        return [
            'cliente_id' => $cliente->id,
            'carro_id' => $carro->id,
            'data_inicio_periodo' => '2024-01-01',
            'data_final_previsto_periodo' => '2024-01-10',
            'data_final_realizado_periodo' => '2024-01-09',
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
            'km_final' => 1500,
        ];
    }

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $setup = $this->createClienteAndCarro();
        $payload = $this->validPayload($setup['cliente'], $setup['carro']);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    LocacaoAssertableJson::schema($json);
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
        $setup = $this->createClienteAndCarro();
        $payload = $this->validPayload($setup['cliente'], $setup['carro']);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }
}
