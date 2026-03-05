<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\UseCases;

use App\Api\Modules\Modelo\Data\ModeloQueryData;
use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Api\Modules\Modelo\UseCases\GetModelosUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class GetModelosUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLengthAwarePaginatorWhenQueryIsValid(): void
    {
        // Arrange
        $query = new ModeloQueryData(search: null, marcaId: null, page: 1, perPage: 15);
        $expectedPaginator = new LengthAwarePaginatorImpl([], 0, 15, 1);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->with(Mockery::type(ModeloQueryData::class))
                    ->andReturn($expectedPaginator);
            }),
        );

        // Act
        $useCase = app()->make(GetModelosUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }
}
