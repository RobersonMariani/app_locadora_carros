<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\UseCases;

use App\Api\Modules\Vistoria\Repositories\VistoriaRepository;
use App\Api\Modules\Vistoria\UseCases\GetVistoriasByLocacaoUseCase;
use App\Models\Vistoria;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class GetVistoriasByLocacaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnCollectionWhenLocacaoExists(): void
    {
        // Arrange
        $expectedCollection = new Collection([
            new Vistoria(['id' => 1, 'locacao_id' => 1, 'tipo' => 'retirada']),
            new Vistoria(['id' => 2, 'locacao_id' => 1, 'tipo' => 'devolucao']),
        ]);

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) use ($expectedCollection) {
                $mock->shouldReceive('getByLocacao')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedCollection);
            }),
        );

        // Act
        $useCase = app()->make(GetVistoriasByLocacaoUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($expectedCollection, $result);
    }

    public function testExecuteShouldReturnEmptyCollectionWhenLocacaoHasNoVistorias(): void
    {
        // Arrange
        $emptyCollection = new Collection([]);

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) use ($emptyCollection) {
                $mock->shouldReceive('getByLocacao')
                    ->once()
                    ->with(1)
                    ->andReturn($emptyCollection);
            }),
        );

        // Act
        $useCase = app()->make(GetVistoriasByLocacaoUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
