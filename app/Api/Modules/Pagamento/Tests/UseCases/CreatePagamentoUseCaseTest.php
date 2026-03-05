<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\UseCases;

use App\Api\Modules\Pagamento\Data\CreatePagamentoData;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Api\Modules\Pagamento\UseCases\CreatePagamentoUseCase;
use App\Models\Pagamento;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class CreatePagamentoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnPagamentoWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreatePagamentoData(
            locacaoId: 1,
            valor: 100.50,
            tipo: 'diaria',
            metodoPagamento: 'pix',
            dataPagamento: '2024-01-15',
        );
        $expectedResult = new Pagamento(['id' => 1, 'valor' => 100.50]);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(CreatePagamentoUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Pagamento::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
