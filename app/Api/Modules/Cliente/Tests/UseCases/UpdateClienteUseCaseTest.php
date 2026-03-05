<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\UseCases;

use App\Api\Modules\Cliente\Data\UpdateClienteData;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Cliente\UseCases\UpdateClienteUseCase;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class UpdateClienteUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnClienteWhenIdExists(): void
    {
        // Arrange
        $existingCliente = new Cliente(['id' => 1, 'nome' => 'João Silva']);
        $updatedCliente = new Cliente(['id' => 1, 'nome' => 'João Silva Atualizado']);
        $data = new UpdateClienteData(nome: 'João Silva Atualizado');

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($existingCliente, $updatedCliente) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($existingCliente);
                $mock->shouldReceive('update')
                    ->once()
                    ->andReturn($updatedCliente);
            }),
        );

        // Act
        $useCase = app()->make(UpdateClienteUseCase::class);
        $result = $useCase->execute(1, $data);

        // Assert
        $this->assertInstanceOf(Cliente::class, $result);
        $this->assertEquals($updatedCliente, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $data = new UpdateClienteData(nome: 'João Silva');

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
                $mock->shouldNotReceive('update');
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(UpdateClienteUseCase::class);
        $useCase->execute(999, $data);
    }
}
