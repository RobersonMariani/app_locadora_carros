<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Locacao\Data\FinalizarLocacaoData;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Api\Modules\Locacao\UseCases\FinalizarLocacaoUseCase;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class FinalizarLocacaoUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function testExecuteShouldReturnLocacaoWhenFinalizarSucceeds(): void
    {
        // Arrange
        $locacao = Locacao::factory()->ativa()->create();
        $locacaoFinalizada = clone $locacao;
        $locacaoFinalizada->status = LocacaoStatusEnum::FINALIZADA;
        $data = new FinalizarLocacaoData(
            kmFinal: 55100,
            dataFinalRealizadoPeriodo: '2024-01-20',
        );

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with($locacao->id)
                    ->andReturn($locacao);
            }),
        );

        $this->instance(
            LocacaoService::class,
            Mockery::mock(LocacaoService::class, function (MockInterface $mock) use ($locacao, $locacaoFinalizada) {
                $mock->shouldReceive('finalizarLocacao')
                    ->once()
                    ->with(
                        Mockery::on(fn ($arg) => $arg instanceof Locacao && $arg->id === $locacao->id),
                        Mockery::on(fn ($arg) => is_array($arg) && isset($arg['km_final']) && isset($arg['data_final_realizado_periodo'])),
                    )
                    ->andReturn($locacaoFinalizada);
            }),
        );

        // Act
        $useCase = app()->make(FinalizarLocacaoUseCase::class);
        $result = $useCase->execute($locacao->id, $data);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals(LocacaoStatusEnum::FINALIZADA, $result->status);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenLocacaoNotFound(): void
    {
        // Arrange
        $data = new FinalizarLocacaoData(
            kmFinal: 55100,
            dataFinalRealizadoPeriodo: '2024-01-20',
        );

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(99999)
                    ->andReturn(null);
            }),
        );

        $this->instance(LocacaoService::class, Mockery::mock(LocacaoService::class, function (MockInterface $mock) {
            $mock->shouldReceive('finalizarLocacao')->never();
        }));

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(FinalizarLocacaoUseCase::class);
        $useCase->execute(99999, $data);
    }
}
