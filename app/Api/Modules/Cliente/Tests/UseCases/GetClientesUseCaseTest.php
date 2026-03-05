<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\UseCases;

use App\Api\Modules\Cliente\Data\ClienteQueryData;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Cliente\UseCases\GetClientesUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class GetClientesUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLengthAwarePaginatorWhenQueryIsValid(): void
    {
        // Arrange
        $query = new ClienteQueryData(search: null, page: 1, perPage: 15);
        $expectedPaginator = new LengthAwarePaginatorImpl([], 0, 15, 1);

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->andReturn($expectedPaginator);
            }),
        );

        // Act
        $useCase = app()->make(GetClientesUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }
}
