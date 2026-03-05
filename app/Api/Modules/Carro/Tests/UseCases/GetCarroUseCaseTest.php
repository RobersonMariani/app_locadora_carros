<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\UseCases;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Carro\UseCases\GetCarroUseCase;
use App\Models\Carro;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class GetCarroUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnCarroWhenIdExists(): void
    {
        // Arrange
        $expectedCarro = new Carro([
            'id' => 1,
            'modelo_id' => 1,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 50000,
        ]);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($expectedCarro) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedCarro);
            }),
        );

        // Act
        $useCase = app()->make(GetCarroUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Carro::class, $result);
        $this->assertEquals($expectedCarro, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(GetCarroUseCase::class);
        $useCase->execute(999);
    }
}
