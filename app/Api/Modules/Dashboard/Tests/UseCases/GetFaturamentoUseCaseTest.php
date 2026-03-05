<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Tests\UseCases;

use App\Api\Modules\Dashboard\Repositories\DashboardRepository;
use App\Api\Modules\Dashboard\UseCases\GetFaturamentoUseCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('dashboard')]
class GetFaturamentoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnFaturamentoMensalWhenPeriodoIsMensal(): void
    {
        // Arrange
        $expectedData = [
            ['periodo' => '2024-01', 'faturamento' => 5000.00, 'quantidade_locacoes' => 5],
            ['periodo' => '2024-02', 'faturamento' => 7500.00, 'quantidade_locacoes' => 8],
        ];

        $this->instance(
            DashboardRepository::class,
            Mockery::mock(DashboardRepository::class, function (MockInterface $mock) use ($expectedData) {
                $mock->shouldReceive('getFaturamento')
                    ->once()
                    ->with('mensal')
                    ->andReturn($expectedData);
            }),
        );

        // Act
        $useCase = app()->make(GetFaturamentoUseCase::class);
        $result = $useCase->execute('mensal');

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedData, $result);
    }

    public function testExecuteShouldReturnFaturamentoSemanalWhenPeriodoIsSemanal(): void
    {
        // Arrange
        $expectedData = [
            ['periodo' => '2024-W01', 'faturamento' => 1500.00, 'quantidade_locacoes' => 2],
        ];

        $this->instance(
            DashboardRepository::class,
            Mockery::mock(DashboardRepository::class, function (MockInterface $mock) use ($expectedData) {
                $mock->shouldReceive('getFaturamento')
                    ->once()
                    ->with('semanal')
                    ->andReturn($expectedData);
            }),
        );

        // Act
        $useCase = app()->make(GetFaturamentoUseCase::class);
        $result = $useCase->execute('semanal');

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($expectedData, $result);
    }

    public function testExecuteShouldUseMensalAsDefaultWhenNoPeriodoProvided(): void
    {
        // Arrange
        $expectedData = [];

        $this->instance(
            DashboardRepository::class,
            Mockery::mock(DashboardRepository::class, function (MockInterface $mock) use ($expectedData) {
                $mock->shouldReceive('getFaturamento')
                    ->once()
                    ->with('mensal')
                    ->andReturn($expectedData);
            }),
        );

        // Act
        $useCase = app()->make(GetFaturamentoUseCase::class);
        $result = $useCase->execute();

        // Assert
        $this->assertIsArray($result);
    }
}
