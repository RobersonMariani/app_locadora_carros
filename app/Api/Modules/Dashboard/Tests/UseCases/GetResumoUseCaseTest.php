<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Tests\UseCases;

use App\Api\Modules\Dashboard\Repositories\DashboardRepository;
use App\Api\Modules\Dashboard\UseCases\GetResumoUseCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('dashboard')]
class GetResumoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnResumoArrayWhenCalled(): void
    {
        // Arrange
        $expectedResumo = [
            'total_marcas' => 5,
            'total_modelos' => 10,
            'total_carros' => 20,
            'total_clientes' => 50,
            'carros_disponiveis' => 15,
            'carros_locados' => 5,
            'locacoes_ativas' => 3,
            'locacoes_reservadas' => 2,
            'faturamento_mes' => 15000.50,
        ];

        $this->instance(
            DashboardRepository::class,
            Mockery::mock(DashboardRepository::class, function (MockInterface $mock) use ($expectedResumo) {
                $mock->shouldReceive('getResumo')
                    ->once()
                    ->andReturn($expectedResumo);
            }),
        );

        // Act
        $useCase = app()->make(GetResumoUseCase::class);
        $result = $useCase->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($expectedResumo, $result);
        $this->assertArrayHasKey('total_marcas', $result);
        $this->assertArrayHasKey('faturamento_mes', $result);
    }
}
