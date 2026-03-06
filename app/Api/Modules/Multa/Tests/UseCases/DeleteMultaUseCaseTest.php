<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\UseCases;

use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Api\Modules\Multa\UseCases\DeleteMultaUseCase;
use App\Models\Multa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class DeleteMultaUseCaseTest extends TestCase
{
    public function testExecuteShouldDeleteMultaWhenIdExists(): void
    {
        // Arrange
        $multa = new Multa([
            'id' => 1,
            'locacao_id' => 1,
            'carro_id' => 1,
            'cliente_id' => 1,
        ]);

        $this->instance(
            MultaRepository::class,
            Mockery::mock(MultaRepository::class, function (MockInterface $mock) use ($multa) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($multa);
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($multa)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteMultaUseCase::class);
        $useCase->execute(1);

        // Assert - no exception thrown
        $this->assertTrue(true);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $this->instance(
            MultaRepository::class,
            Mockery::mock(MultaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(DeleteMultaUseCase::class);
        $useCase->execute(999);
    }
}
