<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\UseCases;

use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Api\Modules\Pagamento\UseCases\GetPagamentosByLocacaoUseCase;
use App\Models\Pagamento;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('pagamento')]
class GetPagamentosByLocacaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnCollectionWhenLocacaoExists(): void
    {
        // Arrange
        $expectedCollection = collect([
            new Pagamento(['id' => 1, 'locacao_id' => 1]),
        ]);

        $this->instance(
            PagamentoRepository::class,
            Mockery::mock(PagamentoRepository::class, function (MockInterface $mock) use ($expectedCollection) {
                $mock->shouldReceive('getByLocacao')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedCollection);
            }),
        );

        // Act
        $useCase = app()->make(GetPagamentosByLocacaoUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }
}
