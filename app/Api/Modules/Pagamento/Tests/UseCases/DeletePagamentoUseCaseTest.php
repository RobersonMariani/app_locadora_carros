<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\UseCases;

use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Api\Modules\Pagamento\UseCases\DeletePagamentoUseCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class DeletePagamentoUseCaseTest extends TestCase
{
    public function testExecuteShouldDeletePagamentoWhenIdExists(): void
    {
        // Arrange
        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('delete')
                    ->once()
                    ->with(1);
            }),
        );

        // Act
        $useCase = app()->make(DeletePagamentoUseCase::class);
        $useCase->execute(1);

        // Assert - mock verifies the call
        $this->assertTrue(true);
    }
}
