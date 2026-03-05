<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Integrations;

use App\Api\Modules\Carro\Tests\Assertables\CarroAssertableJson;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CreateCarroIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/carro';

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $payload = [
            'modelo_id' => $modelo->id,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 50000,
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                CarroAssertableJson::schema($json);
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
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $payload = [
            'modelo_id' => $modelo->id,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 50000,
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }
}
