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
class GetCarroIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT_INDEX = '/api/v1/carro';

    public function testIndexShouldReturnPaginatedCarrosWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id, 'placa' => 'ABC1234']);
        Carro::factory()->create(['modelo_id' => $modelo->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX)
            ->assertOk()
            ->assertJsonFragment(['id' => $carro->id, 'placa' => 'ABC1234']);
    }

    public function testShowShouldReturnCarroWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'/'.$carro->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                CarroAssertableJson::schema($json);
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

    public function testIndexShouldReturnFilteredCarrosWhenCombustivelFilterApplied(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        Carro::factory()->create(['modelo_id' => $modelo->id, 'combustivel' => 'flex', 'placa' => 'ABC1234']);
        Carro::factory()->create(['modelo_id' => $modelo->id, 'combustivel' => 'diesel', 'placa' => 'XYZ9876']);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'?combustivel=flex')
            ->assertOk();

        $response->assertJsonFragment(['placa' => 'ABC1234']);
        $response->assertJsonMissing(['placa' => 'XYZ9876']);
    }

    public function testIndexShouldReturnFilteredCarrosWhenCambioAndCategoriaFiltersApplied(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        Carro::factory()->create([
            'modelo_id' => $modelo->id,
            'cambio' => 'automatico',
            'categoria' => 'sedan',
            'placa' => 'FILTRO1',
        ]);
        Carro::factory()->create([
            'modelo_id' => $modelo->id,
            'cambio' => 'manual',
            'categoria' => 'suv',
            'placa' => 'FILTRO2',
        ]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'?cambio=automatico&categoria=sedan')
            ->assertOk();

        $response->assertJsonFragment(['placa' => 'FILTRO1']);
        $response->assertJsonMissing(['placa' => 'FILTRO2']);
    }
}
