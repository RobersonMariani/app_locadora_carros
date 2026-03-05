<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\UseCases;

use App\Api\Modules\Carro\Data\CreateCarroData;
use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Carro\UseCases\CreateCarroUseCase;
use App\Models\Carro;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CreateCarroUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnCarroWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreateCarroData(
            modeloId: 1,
            placa: 'ABC1234',
            disponivel: true,
            km: 50000,
        );
        $expectedResult = new Carro([
            'id' => 1,
            'modelo_id' => 1,
            'placa' => 'ABC1234',
            'disponivel' => true,
            'km' => 50000,
        ]);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(CreateCarroUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Carro::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
