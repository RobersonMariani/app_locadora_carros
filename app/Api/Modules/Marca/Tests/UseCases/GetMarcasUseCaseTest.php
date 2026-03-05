<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\UseCases;

use App\Api\Modules\Marca\Data\MarcaQueryData;
use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Api\Modules\Marca\UseCases\GetMarcasUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorConcrete;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class GetMarcasUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLengthAwarePaginatorWhenCalled(): void
    {
        // Arrange
        $query = new MarcaQueryData(search: null, page: 1, perPage: 15);
        $paginator = new LengthAwarePaginatorConcrete([], 0, 15, 1);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($query, $paginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->withArgs(function (MarcaQueryData $q) use ($query) {
                        return $q->search === $query->search
                            && $q->page === $query->page
                            && $q->perPage === $query->perPage;
                    })
                    ->andReturn($paginator);
            }),
        );

        // Act
        $useCase = app()->make(GetMarcasUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testExecuteShouldPassSearchToRepositoryWhenSearchProvided(): void
    {
        // Arrange
        $query = new MarcaQueryData(search: 'Toyota', page: 1, perPage: 10);
        $paginator = new LengthAwarePaginatorConcrete([], 0, 10, 1);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($paginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->withArgs(function (MarcaQueryData $q) {
                        return $q->search === 'Toyota';
                    })
                    ->andReturn($paginator);
            }),
        );

        // Act
        $useCase = app()->make(GetMarcasUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
