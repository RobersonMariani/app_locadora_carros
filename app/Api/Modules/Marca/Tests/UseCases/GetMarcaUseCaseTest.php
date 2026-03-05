<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\UseCases;

use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Api\Modules\Marca\UseCases\GetMarcaUseCase;
use App\Models\Marca;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class GetMarcaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnMarcaWhenFound(): void
    {
        // Arrange
        $expectedMarca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => 'imagens/marcas/toyota.png']);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($expectedMarca) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedMarca);
            }),
        );

        // Act
        $useCase = app()->make(GetMarcaUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Marca::class, $result);
        $this->assertEquals($expectedMarca, $result);
    }

    public function testExecuteShouldReturnNullWhenNotFound(): void
    {
        // Arrange
        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act
        $useCase = app()->make(GetMarcaUseCase::class);
        $result = $useCase->execute(999);

        // Assert
        $this->assertNull($result);
    }
}
