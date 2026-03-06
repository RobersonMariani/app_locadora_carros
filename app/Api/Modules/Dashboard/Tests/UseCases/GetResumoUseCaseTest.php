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
            'carros_em_manutencao' => 2,
            'taxa_ocupacao' => 25.0,
            'locacoes_atrasadas' => 1,
            'total_multas_pendentes' => 3,
            'valor_multas_pendentes' => 450.00,
            'total_a_receber' => 3200.00,
            'total_recebido_mes' => 8500.00,
            'manutencoes_proximas' => 4,
            'alertas_nao_lidos' => 5,
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
        $this->assertArrayHasKey('carros_em_manutencao', $result);
        $this->assertArrayHasKey('taxa_ocupacao', $result);
        $this->assertArrayHasKey('locacoes_atrasadas', $result);
        $this->assertArrayHasKey('total_multas_pendentes', $result);
        $this->assertArrayHasKey('valor_multas_pendentes', $result);
        $this->assertArrayHasKey('total_a_receber', $result);
        $this->assertArrayHasKey('total_recebido_mes', $result);
        $this->assertArrayHasKey('manutencoes_proximas', $result);
        $this->assertArrayHasKey('alertas_nao_lidos', $result);
    }
}
