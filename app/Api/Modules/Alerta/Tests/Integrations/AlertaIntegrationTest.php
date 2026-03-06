<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Tests\Integrations;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use App\Api\Modules\Alerta\Tests\Assertables\AlertaAssertableJson;
use App\Models\Alerta;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('alerta')]
class AlertaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT_INDEX = '/api/v1/alerta';

    private const ENDPOINT_COUNT = '/api/v1/alerta/count';

    private const ENDPOINT_MARCAR_TODOS = '/api/v1/alerta/lidos';

    public function testShouldReturnPaginatedListWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        Alerta::factory()->count(3)->create([
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'tipo', 'tipo_label', 'titulo', 'descricao', 'referencia_type', 'referencia_id', 'lido', 'data_alerta', 'created_at', 'updated_at'],
                ],
            ])
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 3)
                    ->has('data.0', function (AssertableJson $item) {
                        AlertaAssertableJson::schema($item);
                    })
                    ->etc();
            });
    }

    public function testShouldReturnCountWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        Alerta::factory()->naoLido()->count(2)->create([
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);
        Alerta::factory()->lido()->count(1)->create([
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_COUNT)
            ->assertOk()
            ->assertJson(['count' => 2]);
    }

    public function testShouldMarcarComoLidoWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        $alerta = Alerta::factory()->naoLido()->create([
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson("/api/v1/alerta/{$alerta->id}/lido")
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $data) {
                    AlertaAssertableJson::schema($data)->where('lido', true);
                })->etc();
            });

        $this->assertTrue($alerta->fresh()->lido);
    }

    public function testShouldMarcarTodosComoLidosWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        Alerta::factory()->naoLido()->count(3)->create([
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson(self::ENDPOINT_MARCAR_TODOS)
            ->assertOk()
            ->assertJson(['marcados' => 3]);

        $this->assertEquals(0, Alerta::query()->where('lido', false)->count());
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT_INDEX)
            ->assertUnauthorized();

        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT_COUNT)
            ->assertUnauthorized();

        $alerta = Alerta::factory()->create();
        $this
            ->withHeader('Accept', 'application/json')
            ->patchJson("/api/v1/alerta/{$alerta->id}/lido")
            ->assertUnauthorized();

        $this
            ->withHeader('Accept', 'application/json')
            ->patchJson(self::ENDPOINT_MARCAR_TODOS)
            ->assertUnauthorized();
    }

    public function testShouldFilterByTipoWhenQueryParamProvided(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = $this->createLocacaoWithRelations();
        Alerta::factory()->create([
            'tipo' => AlertaTipoEnum::LOCACAO_ATRASADA->value,
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);
        Alerta::factory()->create([
            'tipo' => AlertaTipoEnum::MANUTENCAO_PROXIMA->value,
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => $locacao->id,
        ]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'?tipo='.AlertaTipoEnum::LOCACAO_ATRASADA->value)
            ->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(AlertaTipoEnum::LOCACAO_ATRASADA->value, $data[0]['tipo']);
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
