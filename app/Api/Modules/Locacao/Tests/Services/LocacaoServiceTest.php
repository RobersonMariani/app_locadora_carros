<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Services;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Models\Carro;
use App\Models\Locacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class LocacaoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testIniciarLocacaoShouldReturnLocacaoWhenStatusAllowsTransition(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create(['status' => LocacaoStatusEnum::RESERVADA]);
        $locacaoAtualizada = clone $locacao;
        $locacaoAtualizada->status = LocacaoStatusEnum::ATIVA;

        $locacaoRepoMock = Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao, $locacaoAtualizada) {
            $mock->shouldReceive('updateStatus')
                ->once()
                ->with($locacao->id, LocacaoStatusEnum::ATIVA)
                ->andReturn($locacaoAtualizada);
        });

        $carroRepoMock = Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($locacao) {
            $mock->shouldReceive('marcarIndisponivel')
                ->once()
                ->with($locacao->carro_id);
        });

        $this->instance(LocacaoRepository::class, $locacaoRepoMock);
        $this->instance(CarroRepository::class, $carroRepoMock);

        $service = app()->make(LocacaoService::class);

        // Act
        $result = $service->iniciarLocacao($locacao);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals(LocacaoStatusEnum::ATIVA, $result->status);
    }

    public function testIniciarLocacaoShouldThrowWhenStatusDoesNotAllowTransition(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create(['status' => LocacaoStatusEnum::FINALIZADA]);

        $this->instance(LocacaoRepository::class, Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateStatus')->never();
        }));
        $this->instance(CarroRepository::class, Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('marcarIndisponivel')->never();
        }));

        $service = app()->make(LocacaoService::class);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Não é possível iniciar a locação');

        $service->iniciarLocacao($locacao);
    }

    public function testFinalizarLocacaoShouldReturnLocacaoWhenStatusAllowsTransition(): void
    {
        // Arrange
        $locacao = Locacao::factory()->ativa()->create([
            'data_inicio_periodo' => '2024-01-10',
            'data_final_previsto_periodo' => '2024-01-15',
            'valor_diaria' => 100,
            'km_inicial' => 50000,
        ]);
        $dadosFinalizacao = [
            'km_final' => 50100,
            'data_final_realizado_periodo' => '2024-01-15',
        ];

        $locacaoFinalizada = clone $locacao;
        $locacaoFinalizada->status = LocacaoStatusEnum::FINALIZADA;
        $locacaoFinalizada->valor_total = 600;
        $locacaoFinalizada->km_final = 50100;

        $locacaoRepoMock = Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacaoFinalizada) {
            $mock->shouldReceive('finalizar')
                ->once()
                ->andReturn($locacaoFinalizada);
        });

        $carroRepoMock = Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($locacao) {
            $mock->shouldReceive('marcarDisponivel')
                ->once()
                ->with($locacao->carro_id);
        });

        $this->instance(LocacaoRepository::class, $locacaoRepoMock);
        $this->instance(CarroRepository::class, $carroRepoMock);

        $service = app()->make(LocacaoService::class);

        // Act
        $result = $service->finalizarLocacao($locacao, $dadosFinalizacao);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals(LocacaoStatusEnum::FINALIZADA, $result->status);
    }

    public function testFinalizarLocacaoShouldThrowWhenStatusDoesNotAllowTransition(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create(['status' => LocacaoStatusEnum::RESERVADA]);
        $dadosFinalizacao = [
            'km_final' => 50100,
            'data_final_realizado_periodo' => '2024-01-15',
        ];

        $this->instance(LocacaoRepository::class, Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('finalizar')->never();
        }));
        $this->instance(CarroRepository::class, Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('marcarDisponivel')->never();
        }));

        $service = app()->make(LocacaoService::class);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Não é possível finalizar a locação');

        $service->finalizarLocacao($locacao, $dadosFinalizacao);
    }

    public function testCancelarLocacaoShouldReturnLocacaoWhenStatusAllowsTransition(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create(['status' => LocacaoStatusEnum::RESERVADA]);
        $locacaoCancelada = clone $locacao;
        $locacaoCancelada->status = LocacaoStatusEnum::CANCELADA;

        $locacaoRepoMock = Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao, $locacaoCancelada) {
            $mock->shouldReceive('updateStatus')
                ->once()
                ->with($locacao->id, LocacaoStatusEnum::CANCELADA)
                ->andReturn($locacaoCancelada);
        });

        $carroRepoMock = Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('marcarDisponivel')->never();
        });

        $this->instance(LocacaoRepository::class, $locacaoRepoMock);
        $this->instance(CarroRepository::class, $carroRepoMock);

        $service = app()->make(LocacaoService::class);

        // Act
        $result = $service->cancelarLocacao($locacao);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals(LocacaoStatusEnum::CANCELADA, $result->status);
    }

    public function testCancelarLocacaoShouldMarcarCarroDisponivelWhenStatusWasAtiva(): void
    {
        // Arrange
        $locacao = Locacao::factory()->ativa()->create();
        $locacaoCancelada = clone $locacao;
        $locacaoCancelada->status = LocacaoStatusEnum::CANCELADA;

        $locacaoRepoMock = Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacaoCancelada) {
            $mock->shouldReceive('updateStatus')
                ->once()
                ->andReturn($locacaoCancelada);
        });

        $carroRepoMock = Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($locacao) {
            $mock->shouldReceive('marcarDisponivel')
                ->once()
                ->with($locacao->carro_id);
        });

        $this->instance(LocacaoRepository::class, $locacaoRepoMock);
        $this->instance(CarroRepository::class, $carroRepoMock);

        $service = app()->make(LocacaoService::class);

        // Act
        $result = $service->cancelarLocacao($locacao);

        // Assert
        $this->assertEquals(LocacaoStatusEnum::CANCELADA, $result->status);
    }

    public function testValidarDisponibilidadeShouldThrowWhenCarroNotFound(): void
    {
        // Arrange
        $this->instance(CarroRepository::class, Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findById')
                ->once()
                ->with(99999)
                ->andReturn(null);
        }));
        $this->instance(LocacaoRepository::class, Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('hasConflitoPeriodo')->never();
        }));

        $service = app()->make(LocacaoService::class);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Carro não encontrado');

        $service->validarDisponibilidade(99999, '2024-01-01', '2024-01-10');
    }

    public function testValidarDisponibilidadeShouldThrowWhenCarroIndisponivel(): void
    {
        // Arrange
        $carro = new Carro(['id' => 1, 'disponivel' => false]);

        $this->instance(CarroRepository::class, Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($carro) {
            $mock->shouldReceive('findById')
                ->once()
                ->with(1)
                ->andReturn($carro);
        }));
        $this->instance(LocacaoRepository::class, Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('hasConflitoPeriodo')->never();
        }));

        $service = app()->make(LocacaoService::class);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Carro indisponível');

        $service->validarDisponibilidade(1, '2024-01-01', '2024-01-10');
    }
}
