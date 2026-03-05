<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Integrations;

use App\Api\Modules\Carro\Tests\Assertables\CarroAssertableJson;
use App\Models\Carro;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class UpdateCarroIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnUpdatedCarroWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);
        $payload = [
            'placa' => 'XYZ9876',
            'disponivel' => false,
            'km' => 60000,
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/carro/'.$carro->id, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                CarroAssertableJson::schema($json);
            });
    }

    public function testShouldReturnNotFoundWhenIdDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'placa' => 'XYZ9876',
            'disponivel' => false,
            'km' => 60000,
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/carro/99999', $payload)
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->putJson('/api/v1/carro/1', ['placa' => 'XYZ9876'])
            ->assertUnauthorized();
    }
}
