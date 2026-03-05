<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\UseCases;

use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Cliente\UseCases\GetClienteUseCase;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class GetClienteUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnClienteWhenIdExists(): void
    {
        // Arrange
        $expectedCliente = new Cliente(['id' => 1, 'nome' => 'João Silva']);

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($expectedCliente) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedCliente);
            }),
        );

        // Act
        $useCase = app()->make(GetClienteUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Cliente::class, $result);
        $this->assertEquals($expectedCliente, $result);
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
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(GetClienteUseCase::class);
        $useCase->execute(999);
    }
}
