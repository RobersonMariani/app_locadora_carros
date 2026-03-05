<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\UseCases;

use App\Api\Modules\Auth\UseCases\LogoutUseCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('auth')]
class LogoutUseCaseTest extends TestCase
{
    public function testExecuteShouldCallLogoutOnGuardWhenInvoked(): void
    {
        // Arrange
        $mockGuard = Mockery::mock(Guard::class);
        $mockGuard->shouldReceive('logout')->once();

        Auth::shouldReceive('guard')
            ->with('api')
            ->andReturn($mockGuard);

        // Act
        $useCase = app()->make(LogoutUseCase::class);
        $useCase->execute();

        // Assert
        $this->addToAssertionCount(1);
    }
}
