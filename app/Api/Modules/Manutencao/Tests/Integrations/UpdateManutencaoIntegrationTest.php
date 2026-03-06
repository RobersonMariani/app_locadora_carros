<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Integrations;

use App\Api\Modules\Manutencao\Tests\Assertables\ManutencaoAssertableJson;
use App\Models\Carro;
use App\Models\Manutencao;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class UpdateManutencaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/manutencao';

    private function createCarro(): Carro
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        return Carro::factory()->create(['modelo_id' => $modelo->id]);
    }

    public function testShouldReturnUpdatedManutencaoWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create([
            'carro_id' => $carro->id,
            'status' => 'agendada',
        ]);
        $payload = [
            'descricao' => 'Descrição atualizada',
            'valor' => 350.00,
            'status' => 'em_andamento',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/'.$manutencao->id, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                ManutencaoAssertableJson::schema($json);
            })
            ->assertJsonFragment(['descricao' => 'Descrição atualizada', 'status' => 'em_andamento']);
    }

    public function testShouldMarkCarroDisponivelWhenStatusUpdatedToConcluida(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $carro->update(['disponivel' => false]);
        $manutencao = Manutencao::factory()->create([
            'carro_id' => $carro->id,
            'status' => 'em_andamento',
        ]);

        // Act
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/'.$manutencao->id, ['status' => 'concluida'])
            ->assertOk();

        // Assert
        $carro->refresh();
        $this->assertTrue($carro->disponivel);
    }

    public function testShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = ['descricao' => 'Atualização', 'status' => 'concluida'];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(self::ENDPOINT.'/99999', $payload)
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create(['carro_id' => $carro->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->putJson(self::ENDPOINT.'/'.$manutencao->id, ['status' => 'concluida'])
            ->assertUnauthorized();
    }
}
