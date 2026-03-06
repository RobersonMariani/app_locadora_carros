<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\Integrations;

use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Api\Modules\Vistoria\Tests\Assertables\VistoriaAssertableJson;
use App\Models\Locacao;
use App\Models\User;
use App\Models\Vistoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class GetVistoriasByLocacaoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function endpoint(int $locacaoId): string
    {
        return "/api/v1/locacao/{$locacaoId}/vistoria";
    }

    public function testShouldReturnVistoriasWhenLocacaoHasVistorias(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        Vistoria::factory()->create([
            'locacao_id' => $locacao->id,
            'tipo' => VistoriaTipoEnum::RETIRADA,
        ]);
        Vistoria::factory()->create([
            'locacao_id' => $locacao->id,
            'tipo' => VistoriaTipoEnum::DEVOLUCAO,
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->endpoint($locacao->id))
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data')
                    ->whereType('data', 'array')
                    ->etc();
            })
            ->assertJsonCount(2, 'data');
    }

    public function testShouldReturnEmptyArrayWhenLocacaoHasNoVistorias(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->endpoint($locacao->id))
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data')
                    ->whereType('data', 'array')
                    ->etc();
            })
            ->assertJsonCount(0, 'data');
    }

    public function testShouldReturnVistoriaWithCorrectSchemaWhenPresent(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        Vistoria::factory()->create([
            'locacao_id' => $locacao->id,
            'tipo' => VistoriaTipoEnum::RETIRADA,
        ]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->endpoint($locacao->id));

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('data.0', fn (AssertableJson $json) => VistoriaAssertableJson::schema($json))
            ->etc());
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson($this->endpoint($locacao->id))
            ->assertUnauthorized();
    }
}
