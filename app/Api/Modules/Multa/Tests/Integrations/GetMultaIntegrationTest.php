<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Integrations;

use App\Api\Modules\Multa\Tests\Assertables\MultaAssertableJson;
use App\Models\Multa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class GetMultaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT_INDEX = '/api/v1/multa';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function testIndexShouldReturnPaginatedMultasWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multa = Multa::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX)
            ->assertOk()
            ->assertJsonFragment(['id' => $multa->id]);
    }

    public function testShowShouldReturnMultaWhenIdExists(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multa = Multa::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'/'.$multa->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                MultaAssertableJson::schema($json);
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

    public function testIndexByLocacaoShouldReturnMultasWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multa = Multa::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/locacao/'.$multa->locacao_id.'/multa')
            ->assertOk()
            ->assertJsonFragment(['id' => $multa->id]);
    }

    public function testIndexByClienteShouldReturnMultasWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multa = Multa::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/cliente/'.$multa->cliente_id.'/multa')
            ->assertOk()
            ->assertJsonFragment(['id' => $multa->id]);
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

    public function testIndexByLocacaoShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/v1/locacao/1/multa')
            ->assertUnauthorized();
    }

    public function testIndexByClienteShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson('/api/v1/cliente/1/multa')
            ->assertUnauthorized();
    }

    public function testIndexShouldReturnFilteredMultasWhenStatusFilterApplied(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $multaPendente = Multa::factory()->pendente()->create();
        $multaPaga = Multa::factory()->paga()->create();

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT_INDEX.'?status=pendente')
            ->assertOk();

        $response->assertJsonFragment(['id' => $multaPendente->id]);
        $response->assertJsonMissing(['id' => $multaPaga->id]);
    }
}
