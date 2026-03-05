<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\UseCases\DeleteLocacaoUseCase;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class DeleteLocacaoUseCaseTest extends TestCase
{
    public function testExecuteShouldDeleteLocacaoWhenIdExists(): void
    {
        // Arrange
        $locacao = new Locacao([
            'id' => 1,
            'cliente_id' => 1,
            'carro_id' => 1,
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
        ]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($locacao);
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($locacao)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteLocacaoUseCase::class);
        $useCase->execute(1);

        // Assert
        $this->assertTrue(true);
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
                $mock->shouldNotReceive('delete');
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(DeleteLocacaoUseCase::class);
        $useCase->execute(999);
    }
}
