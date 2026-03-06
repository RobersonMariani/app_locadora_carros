<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Tests\Integrations;

use App\Api\Modules\Dashboard\Tests\Assertables\ResumoAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('dashboard')]
class GetResumoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/dashboard/resumo';

    public function testShouldReturnResumoWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(self::ENDPOINT)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                ResumoAssertableJson::schema($json);
            });
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->getJson(self::ENDPOINT)
            ->assertUnauthorized();
    }
}
