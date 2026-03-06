<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\UseCases;

use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use App\Api\Modules\Manutencao\UseCases\GetManutencaoUseCase;
use App\Models\Manutencao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class GetManutencaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnManutencaoWhenIdExists(): void
    {
        // Arrange
        $expectedResult = new Manutencao([
            'id' => 1,
            'carro_id' => 1,
            'descricao' => 'Troca de óleo',
        ]);

        $this->instance(
            ManutencaoRepository::class,
            Mockery::mock(ManutencaoRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(GetManutencaoUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Manutencao::class, $result);
        $this->assertSame($expectedResult, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $this->instance(
            ManutencaoRepository::class,
            Mockery::mock(ManutencaoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(99999)
                    ->andReturn(null);
            }),
        );

        $this->expectException(ModelNotFoundException::class);

        // Act
        $useCase = app()->make(GetManutencaoUseCase::class);
        $useCase->execute(99999);
    }
}
