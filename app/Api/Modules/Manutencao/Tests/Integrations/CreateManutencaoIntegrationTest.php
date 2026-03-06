<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Integrations;

use App\Api\Modules\Manutencao\Tests\Assertables\ManutencaoAssertableJson;
use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class CreateManutencaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/manutencao';

    private function createCarro(): Carro
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        return Carro::factory()->create(['modelo_id' => $modelo->id]);
    }

    private function validPayload(Carro $carro): array
    {
        return [
            'carro_id' => $carro->id,
            'tipo' => 'preventiva',
            'descricao' => 'Troca de óleo',
            'valor' => 250.50,
            'km_manutencao' => 50000,
            'data_manutencao' => '2024-01-15',
            'status' => 'agendada',
        ];
    }

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $payload = $this->validPayload($carro);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                ManutencaoAssertableJson::schema($json);
            });
    }

    public function testShouldMarkCarroIndisponivelWhenStatusIsEmAndamento(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $payload = array_merge($this->validPayload($carro), ['status' => 'em_andamento']);

        // Act
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated();

        // Assert
        $carro->refresh();
        $this->assertFalse($carro->disponivel);
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
        $carro = $this->createCarro();
        $payload = $this->validPayload($carro);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }
}
