<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\UseCases;

use App\Api\Modules\Multa\Data\CreateMultaData;
use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Api\Modules\Multa\UseCases\CreateMultaUseCase;
use App\Models\Multa;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class CreateMultaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnMultaWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreateMultaData(
            locacaoId: 1,
            carroId: 1,
            clienteId: 1,
            valor: 150.50,
            dataInfracao: '2024-01-15',
            descricao: 'Excesso de velocidade',
            status: 'pendente',
        );
        $expectedResult = new Multa([
            'id' => 1,
            'locacao_id' => 1,
            'carro_id' => 1,
            'cliente_id' => 1,
            'valor' => 150.50,
            'descricao' => 'Excesso de velocidade',
        ]);

        $this->instance(
            MultaRepository::class,
            Mockery::mock(MultaRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(CreateMultaUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Multa::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
