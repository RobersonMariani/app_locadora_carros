<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\UseCases;

use App\Api\Modules\Auth\UseCases\RefreshTokenUseCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('auth')]
class RefreshTokenUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnNewTokenWhenRefreshIsCalled(): void
    {
        // Arrange
        $expectedToken = 'new-jwt-token-456';

        $mockGuard = Mockery::mock(Guard::class);
        $mockGuard->shouldReceive('refresh')
            ->once()
            ->andReturn($expectedToken);

        Auth::shouldReceive('guard')
            ->with('api')
            ->andReturn($mockGuard);

        // Act
        $useCase = app()->make(RefreshTokenUseCase::class);
        $result = $useCase->execute();

        // Assert
        $this->assertSame($expectedToken, $result);
    }
}
