<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Api\Modules\Locacao\UseCases\IniciarLocacaoUseCase;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class IniciarLocacaoUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function testExecuteShouldReturnLocacaoWhenIniciarSucceeds(): void
    {
        // Arrange
        $locacao = Locacao::factory()->create(['status' => LocacaoStatusEnum::RESERVADA]);
        $locacaoAtualizada = clone $locacao;
        $locacaoAtualizada->status = LocacaoStatusEnum::ATIVA;

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with($locacao->id)
                    ->andReturn($locacao);
                $mock->shouldReceive('updateStatus')->never();
            }),
        );

        $this->instance(
            LocacaoService::class,
            Mockery::mock(LocacaoService::class, function (MockInterface $mock) use ($locacao, $locacaoAtualizada) {
                $mock->shouldReceive('iniciarLocacao')
                    ->once()
                    ->with(Mockery::on(fn ($arg) => $arg instanceof Locacao && $arg->id === $locacao->id))
                    ->andReturn($locacaoAtualizada);
            }),
        );

        // Act
        $useCase = app()->make(IniciarLocacaoUseCase::class);
        $result = $useCase->execute($locacao->id);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals(LocacaoStatusEnum::ATIVA, $result->status);
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
            $mock->shouldReceive('iniciarLocacao')->never();
        }));

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(IniciarLocacaoUseCase::class);
        $useCase->execute(99999);
    }
}
