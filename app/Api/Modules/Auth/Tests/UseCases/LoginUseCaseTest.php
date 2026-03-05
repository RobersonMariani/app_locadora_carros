<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\UseCases;

use App\Api\Modules\Auth\Data\LoginData;
use App\Api\Modules\Auth\UseCases\LoginUseCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('auth')]
class LoginUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnTokenWhenCredentialsAreValid(): void
    {
        // Arrange
        $data = new LoginData(email: 'user@example.com', password: 'password123');
        $expectedToken = 'jwt-token-123';

        $mockGuard = Mockery::mock(Guard::class);
        $mockGuard->shouldReceive('attempt')
            ->once()
            ->with($data->toCredentials())
            ->andReturn($expectedToken);

        Auth::shouldReceive('guard')
            ->with('api')
            ->andReturn($mockGuard);

        // Act
        $useCase = app()->make(LoginUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertSame($expectedToken, $result);
    }

    public function testExecuteShouldReturnNullWhenCredentialsAreInvalid(): void
    {
        // Arrange
        $data = new LoginData(email: 'user@example.com', password: 'wrong-password');

        $mockGuard = Mockery::mock(Guard::class);
        $mockGuard->shouldReceive('attempt')
            ->once()
            ->with($data->toCredentials())
            ->andReturn(false);

        Auth::shouldReceive('guard')
            ->with('api')
            ->andReturn($mockGuard);

        // Act
        $useCase = app()->make(LoginUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertNull($result);
    }
}
