<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Integrations;

use App\Api\Modules\Marca\Tests\Assertables\MarcaAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class CreateMarcaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/v1/marca';

    public function testShouldReturnCreatedWhenDataIsValid(): void
    {
        // Arrange
        Storage::fake('public');
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'nome' => 'Toyota',
            'imagem' => UploadedFile::fake()->image('marca.png'),
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                MarcaAssertableJson::schema($json);
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
            ->post(self::ENDPOINT, [])
            ->assertUnprocessable();
    }

    public function testShouldReturnUnprocessableWhenImagemInvalidMime(): void
    {
        // Arrange
        $user = User::factory()->create(['password' => 'password']);
        $token = auth('api')->login($user);
        $payload = [
            'nome' => 'Toyota',
            'imagem' => UploadedFile::fake()->image('marca.jpg'),
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertUnprocessable();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, [
                'nome' => 'Toyota',
                'imagem' => UploadedFile::fake()->image('marca.png'),
            ])
            ->assertUnauthorized();
    }
}
