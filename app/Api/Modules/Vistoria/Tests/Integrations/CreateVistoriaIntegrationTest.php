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
class CreateVistoriaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function endpoint(int $locacaoId): string
    {
        return "/api/v1/locacao/{$locacaoId}/vistoria";
    }

    private function validPayload(string $tipo = 'retirada'): array
    {
        return [
            'tipo' => $tipo,
            'combustivel_nivel' => 'metade',
            'km_registrado' => 50000,
            'observacoes' => 'Observação da vistoria',
            'data_vistoria' => '2024-01-15',
        ];
    }

    public function testShouldReturnCreatedWhenDataIsValidAndRetirada(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        $payload = $this->validPayload('retirada');

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson($this->endpoint($locacao->id), $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    VistoriaAssertableJson::schema($json);
                })->etc();
            });
    }

    public function testShouldReturnCreatedWhenDataIsValidAndDevolucaoAfterRetirada(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        Vistoria::factory()->create([
            'locacao_id' => $locacao->id,
            'tipo' => VistoriaTipoEnum::RETIRADA,
        ]);
        $payload = $this->validPayload('devolucao');

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson($this->endpoint($locacao->id), $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    VistoriaAssertableJson::schema($json);
                })->etc();
            });
    }

    public function testShouldReturnUnprocessableWhenRetiradaIsDuplicate(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        Vistoria::factory()->create([
            'locacao_id' => $locacao->id,
            'tipo' => VistoriaTipoEnum::RETIRADA,
        ]);
        $payload = $this->validPayload('retirada');

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson($this->endpoint($locacao->id), $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['tipo']);
    }

    public function testShouldReturnUnprocessableWhenDevolucaoWithoutRetirada(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $locacao = Locacao::factory()->create();
        $payload = $this->validPayload('devolucao');

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson($this->endpoint($locacao->id), $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['tipo']);
    }

    public function testShouldReturnUnprocessableWhenDevolucaoIsDuplicate(): void
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
        $payload = $this->validPayload('devolucao');

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson($this->endpoint($locacao->id), $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['tipo']);
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create();
        $payload = $this->validPayload();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson($this->endpoint($locacao->id), $payload)
            ->assertUnauthorized();
    }
}
