<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\UseCases\GetLocacaoUseCase;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class GetLocacaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLocacaoWhenIdExists(): void
    {
        // Arrange
        $expectedLocacao = new Locacao([
            'id' => 1,
            'cliente_id' => 1,
            'carro_id' => 1,
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
        ]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($expectedLocacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedLocacao);
            }),
        );

        // Act
        $useCase = app()->make(GetLocacaoUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals($expectedLocacao, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(GetLocacaoUseCase::class);
        $useCase->execute(999);
    }
}
