<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\UseCases;

use App\Api\Modules\Pagamento\Data\PagamentoQueryData;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Api\Modules\Pagamento\UseCases\GetPagamentosUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class GetPagamentosUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnPaginatorWhenQueryIsValid(): void
    {
        // Arrange
        $query = new PagamentoQueryData(page: 1, perPage: 15);
        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAll')
                    ->once()
                    ->with(Mockery::on(fn ($arg) => $arg instanceof PagamentoQueryData))
                    ->andReturn($expectedPaginator);
            }),
        );

        // Act
        $useCase = app()->make(GetPagamentosUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }
}
