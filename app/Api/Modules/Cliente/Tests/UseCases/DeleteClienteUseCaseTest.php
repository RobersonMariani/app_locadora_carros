<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\UseCases;

use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Cliente\UseCases\DeleteClienteUseCase;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class DeleteClienteUseCaseTest extends TestCase
{
    public function testExecuteShouldDeleteClienteWhenIdExists(): void
    {
        // Arrange
        $cliente = new Cliente(['id' => 1, 'nome' => 'João Silva']);

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($cliente) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($cliente);
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($cliente)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteClienteUseCase::class);
        $useCase->execute(1);

        // Assert - no exception thrown
        $this->assertTrue(true);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
                $mock->shouldNotReceive('delete');
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(DeleteClienteUseCase::class);
        $useCase->execute(999);
    }
}
