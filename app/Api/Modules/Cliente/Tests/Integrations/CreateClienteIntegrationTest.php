<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Integrations;

use App\Api\Modules\Cliente\Tests\Assertables\ClienteAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class CreateClienteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/cliente';

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = ['nome' => 'João Silva'];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                ClienteAssertableJson::schema($json);
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
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT, ['nome' => 'João Silva'])
            ->assertUnauthorized();
    }
}
