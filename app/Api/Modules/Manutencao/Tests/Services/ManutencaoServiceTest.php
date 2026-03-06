<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Services;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Api\Modules\Manutencao\Services\ManutencaoService;
use App\Models\Manutencao;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class ManutencaoServiceTest extends TestCase
{
    public function testAplicarStatusCarroShouldMarcarIndisponivelWhenStatusIsEmAndamento(): void
    {
        // Arrange
        $manutencao = new Manutencao([
            'id' => 1,
            'carro_id' => 10,
            'status' => ManutencaoStatusEnum::EM_ANDAMENTO,
        ]);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('marcarIndisponivel')
                    ->once()
                    ->with(10);
                $mock->shouldNotReceive('marcarDisponivel');
            }),
        );

        // Act
        $service = app()->make(ManutencaoService::class);
        $service->aplicarStatusCarro($manutencao);

        // Assert - verified via mock expectations
        $this->addToAssertionCount(1);
    }

    public function testAplicarStatusCarroShouldMarcarDisponivelWhenStatusIsConcluida(): void
    {
        // Arrange
        $manutencao = new Manutencao([
            'id' => 1,
            'carro_id' => 20,
            'status' => ManutencaoStatusEnum::CONCLUIDA,
        ]);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
                $mock->shouldNotReceive('marcarIndisponivel');
                $mock->shouldReceive('marcarDisponivel')
                    ->once()
                    ->with(20);
            }),
        );

        // Act
        $service = app()->make(ManutencaoService::class);
        $service->aplicarStatusCarro($manutencao);

        // Assert - verified via mock expectations
        $this->addToAssertionCount(1);
    }

    public function testAplicarStatusCarroShouldMarcarDisponivelWhenStatusIsCancelada(): void
    {
        // Arrange
        $manutencao = new Manutencao([
            'id' => 1,
            'carro_id' => 30,
            'status' => ManutencaoStatusEnum::CANCELADA,
        ]);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
                $mock->shouldNotReceive('marcarIndisponivel');
                $mock->shouldReceive('marcarDisponivel')
                    ->once()
                    ->with(30);
            }),
        );

        // Act
        $service = app()->make(ManutencaoService::class);
        $service->aplicarStatusCarro($manutencao);

        // Assert - verified via mock expectations
        $this->addToAssertionCount(1);
    }

    public function testAplicarStatusCarroShouldNotChangeCarroWhenStatusIsAgendada(): void
    {
        // Arrange
        $manutencao = new Manutencao([
            'id' => 1,
            'carro_id' => 40,
            'status' => ManutencaoStatusEnum::AGENDADA,
        ]);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
                $mock->shouldNotReceive('marcarIndisponivel');
                $mock->shouldNotReceive('marcarDisponivel');
            }),
        );

        // Act
        $service = app()->make(ManutencaoService::class);
        $service->aplicarStatusCarro($manutencao);

        // Assert - verified via mock expectations
        $this->addToAssertionCount(1);
    }
}
