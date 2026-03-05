<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\Integrations;

use App\Api\Modules\Auth\Tests\Assertables\AuthAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('auth')]
class AuthIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT_LOGIN = '/api/login';

    private const ENDPOINT_REFRESH = '/api/refresh';

    private const ENDPOINT_ME = '/api/v1/me';

    private const ENDPOINT_LOGOUT = '/api/v1/logout';

    public function testLoginShouldReturnTokenWhenCredentialsAreValid(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);
        $payload = [
            'email' => 'user@example.com',
            'password' => 'password',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT_LOGIN, $payload)
            ->assertOk()
            ->assertJsonStructure(['token'])
            ->assertJson(fn (AssertableJson $json) => $json->whereType('token', 'string')->etc());
    }

    public function testLoginShouldReturnForbiddenWhenCredentialsAreInvalid(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);
        $payload = [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT_LOGIN, $payload)
            ->assertForbidden()
            ->assertJson(['erro' => 'Usuário ou senha inválido']);
    }

    public function testMeShouldReturnUserDataWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT_ME)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                AuthAssertableJson::schema($json);
            });
    }

    public function testMeShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT_ME)
            ->assertUnauthorized();
    }

    public function testLogoutShouldReturnSuccessWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT_LOGOUT)
            ->assertOk()
            ->assertJson(['msg' => 'O logout foi feito com sucesso']);
    }

    public function testLogoutShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT_LOGOUT)
            ->assertUnauthorized();
    }

    public function testRefreshShouldReturnNewTokenWhenValidTokenProvided(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(self::ENDPOINT_REFRESH)
            ->assertOk()
            ->assertJsonStructure(['token'])
            ->assertJson(fn (AssertableJson $json) => $json->whereType('token', 'string')->etc());
    }

    public function testRefreshShouldReturnServerErrorWhenNoTokenProvided(): void
    {
        // Act & Assert - JWT throws JWTException (500) when token is missing
        $this
            ->withHeader('Accept', 'application/json')
            ->postJson(self::ENDPOINT_REFRESH)
            ->assertStatus(500);
    }
}
