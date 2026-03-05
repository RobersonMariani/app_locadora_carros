<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\UseCases;

use App\Api\Modules\Auth\UseCases\GetAuthenticatedUserUseCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('auth')]
class GetAuthenticatedUserUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnUserWhenAuthenticated(): void
    {
        // Arrange
        $user = new User(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com']);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        // Act
        $useCase = app()->make(GetAuthenticatedUserUseCase::class);
        $result = $useCase->execute();

        // Assert
        $this->assertSame($user, $result);
    }

    public function testExecuteShouldReturnNullWhenNotAuthenticated(): void
    {
        // Arrange
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(null);

        // Act
        $useCase = app()->make(GetAuthenticatedUserUseCase::class);
        $result = $useCase->execute();

        // Assert
        $this->assertNull($result);
    }
}
