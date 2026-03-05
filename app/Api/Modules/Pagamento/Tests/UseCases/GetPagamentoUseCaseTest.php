<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\UseCases;

use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Api\Modules\Pagamento\UseCases\GetPagamentoUseCase;
use App\Models\Pagamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class GetPagamentoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnPagamentoWhenFound(): void
    {
        // Arrange
        $expectedPagamento = new Pagamento(['id' => 1, 'valor' => 100.50]);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) use ($expectedPagamento) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedPagamento);
            }),
        );

        // Act
        $useCase = app()->make(GetPagamentoUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Pagamento::class, $result);
        $this->assertEquals($expectedPagamento, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenNotFound(): void
    {
        // Arrange
        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(99999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(GetPagamentoUseCase::class);
        $useCase->execute(99999);
    }
}
