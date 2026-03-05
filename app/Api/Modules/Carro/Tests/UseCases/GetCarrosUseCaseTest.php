<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\UseCases;

use App\Api\Modules\Carro\Data\CarroQueryData;
use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Carro\UseCases\GetCarrosUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class GetCarrosUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLengthAwarePaginatorWhenQueryIsValid(): void
    {
        // Arrange
        $query = new CarroQueryData(search: null, page: 1, perPage: 15);
        $expectedPaginator = new LengthAwarePaginatorImpl([], 0, 15, 1);

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->with(Mockery::type(CarroQueryData::class))
                    ->andReturn($expectedPaginator);
            }),
        );

        // Act
        $useCase = app()->make(GetCarrosUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }
}
