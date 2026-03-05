<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\UseCases;

use App\Api\Modules\Pagamento\Data\UpdatePagamentoData;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Api\Modules\Pagamento\UseCases\UpdatePagamentoUseCase;
use App\Models\Pagamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class UpdatePagamentoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnUpdatedPagamentoWhenDataIsValid(): void
    {
        // Arrange
        $data = new UpdatePagamentoData(valor: 200.00);
        $expectedResult = new Pagamento(['id' => 1, 'valor' => 200.00]);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('update')
                    ->once()
                    ->with(1, ['valor' => 200.00])
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(UpdatePagamentoUseCase::class);
        $result = $useCase->execute(1, $data);

        // Assert
        $this->assertInstanceOf(Pagamento::class, $result);
        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteShouldReturnPagamentoWhenDataIsEmpty(): void
    {
        // Arrange
        $data = new UpdatePagamentoData;
        $existingPagamento = new Pagamento(['id' => 1, 'valor' => 100.00]);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) use ($existingPagamento) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($existingPagamento);
            }),
        );

        // Act
        $useCase = app()->make(UpdatePagamentoUseCase::class);
        $result = $useCase->execute(1, $data);

        // Assert
        $this->assertInstanceOf(Pagamento::class, $result);
        $this->assertEquals($existingPagamento, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenPagamentoNotFound(): void
    {
        // Arrange
        $data = new UpdatePagamentoData(valor: 200.00);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('update')
                    ->once()
                    ->with(99999, ['valor' => 200.00])
                    ->andThrow(new ModelNotFoundException);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(UpdatePagamentoUseCase::class);
        $useCase->execute(99999, $data);
    }
}
