<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Tests\UseCases;

use App\Api\Modules\Dashboard\Repositories\DashboardRepository;
use App\Api\Modules\Dashboard\UseCases\GetLocacoesPorStatusUseCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('dashboard')]
class GetLocacoesPorStatusUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLocacoesPorStatusArrayWhenCalled(): void
    {
        // Arrange
        $expectedData = [
            ['status' => 'reservada', 'label' => 'Reservada', 'quantidade' => 2],
            ['status' => 'ativa', 'label' => 'Ativa', 'quantidade' => 3],
            ['status' => 'finalizada', 'label' => 'Finalizada', 'quantidade' => 10],
            ['status' => 'cancelada', 'label' => 'Cancelada', 'quantidade' => 1],
        ];

        $this->instance(
            DashboardRepository::class,
            Mockery::mock(DashboardRepository::class, function (MockInterface $mock) use ($expectedData) {
                $mock->shouldReceive('getLocacoesPorStatus')
                    ->once()
                    ->andReturn($expectedData);
            }),
        );

        // Act
        $useCase = app()->make(GetLocacoesPorStatusUseCase::class);
        $result = $useCase->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertEquals($expectedData, $result);
    }
}
