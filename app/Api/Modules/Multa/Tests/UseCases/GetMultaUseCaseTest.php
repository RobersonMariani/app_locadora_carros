<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\UseCases;

use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Api\Modules\Multa\UseCases\GetMultaUseCase;
use App\Models\Multa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class GetMultaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnMultaWhenIdExists(): void
    {
        // Arrange
        $expectedMulta = new Multa([
            'id' => 1,
            'locacao_id' => 1,
            'carro_id' => 1,
            'cliente_id' => 1,
            'valor' => 150.50,
            'descricao' => 'Excesso de velocidade',
        ]);

        $this->instance(
            MultaRepository::class,
            Mockery::mock(MultaRepository::class, function (MockInterface $mock) use ($expectedMulta) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedMulta);
            }),
        );

        // Act
        $useCase = app()->make(GetMultaUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Multa::class, $result);
        $this->assertEquals($expectedMulta, $result);
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

        $useCase = app()->make(GetMultaUseCase::class);
        $useCase->execute(999);
    }
}
