<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Api\Modules\Locacao\UseCases\CancelarLocacaoUseCase;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class CancelarLocacaoUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function testExecuteShouldReturnLocacaoWhenCancelarSucceeds(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create(['status' => LocacaoStatusEnum::RESERVADA]);
        $locacaoCancelada = clone $locacao;
        $locacaoCancelada->status = LocacaoStatusEnum::CANCELADA;

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
            Mockery::mock(LocacaoService::class, function (MockInterface $mock) use ($locacao, $locacaoCancelada) {
                $mock->shouldReceive('cancelarLocacao')
                    ->once()
                    ->with(Mockery::on(fn ($arg) => $arg instanceof Locacao && $arg->id === $locacao->id))
                    ->andReturn($locacaoCancelada);
            }),
        );

        // Act
        $useCase = app()->make(CancelarLocacaoUseCase::class);
        $result = $useCase->execute($locacao->id);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals(LocacaoStatusEnum::CANCELADA, $result->status);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenLocacaoNotFound(): void
    {
        // Arrange
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
            $mock->shouldReceive('cancelarLocacao')->never();
        }));

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(CancelarLocacaoUseCase::class);
        $useCase->execute(99999);
    }
}
