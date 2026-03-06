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
class GetManutencaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT_INDEX = '/api/v1/manutencao';

    private function createCarro(): Carro
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);

        return Carro::factory()->create(['modelo_id' => $modelo->id]);
    }

    public function testIndexShouldReturnPaginatedManutencoesWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create([
            'carro_id' => $carro->id,
            'descricao' => 'Manutenção de teste',
        ]);
        Manutencao::factory()->create(['carro_id' => $carro->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX)
            ->assertOk()
            ->assertJsonFragment(['id' => $manutencao->id, 'descricao' => 'Manutenção de teste']);
    }

    public function testShowShouldReturnManutencaoWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create(['carro_id' => $carro->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'/'.$manutencao->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                ManutencaoAssertableJson::schema($json);
            });
    }

    public function testShowShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'/99999')
            ->assertNotFound();
    }

    public function testIndexByCarroShouldReturnManutencoesWhenCarroExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        $manutencao = Manutencao::factory()->create([
            'carro_id' => $carro->id,
            'descricao' => 'Manutenção do carro específico',
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/carro/'.$carro->id.'/manutencao')
            ->assertOk()
            ->assertJsonFragment(['descricao' => 'Manutenção do carro específico']);
    }

    public function testIndexByCarroShouldReturnNotFoundWhenCarroDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/carro/99999/manutencao')
            ->assertNotFound();
    }

    public function testProximasShouldReturnManutencoesWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        Manutencao::factory()->create([
            'carro_id' => $carro->id,
            'data_proxima' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'agendada',
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/manutencao/proximas')
            ->assertOk();
    }

    public function testIndexShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT_INDEX)
            ->assertUnauthorized();
    }

    public function testShowShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT_INDEX.'/1')
            ->assertUnauthorized();
    }

    public function testIndexShouldReturnFilteredManutencoesWhenTipoFilterApplied(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $carro = $this->createCarro();
        Manutencao::factory()->create(['carro_id' => $carro->id, 'tipo' => 'preventiva', 'descricao' => 'Preventiva 1']);
        Manutencao::factory()->create(['carro_id' => $carro->id, 'tipo' => 'corretiva', 'descricao' => 'Corretiva 1']);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'?tipo=preventiva')
            ->assertOk();

        $response->assertJsonFragment(['descricao' => 'Preventiva 1']);
        $response->assertJsonMissing(['descricao' => 'Corretiva 1']);
    }
}
