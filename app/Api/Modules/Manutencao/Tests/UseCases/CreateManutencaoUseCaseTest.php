<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\UseCases;

use App\Api\Modules\Manutencao\Data\CreateManutencaoData;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use App\Api\Modules\Manutencao\Services\ManutencaoService;
use App\Api\Modules\Manutencao\UseCases\CreateManutencaoUseCase;
use App\Models\Manutencao;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('manutencao')]
class CreateManutencaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnManutencaoAndCallServiceWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreateManutencaoData(
            carroId: 1,
            tipo: 'preventiva',
            descricao: 'Troca de óleo',
            valor: 250.50,
            kmManutencao: 50000,
            dataManutencao: '2024-01-15',
            status: 'em_andamento',
        );
        $expectedResult = new Manutencao([
            'id' => 1,
            'carro_id' => 1,
            'tipo' => 'preventiva',
            'descricao' => 'Troca de óleo',
            'status' => 'em_andamento',
        ]);

        $this->instance(
            ManutencaoRepository::class,
            Mockery::mock(ManutencaoRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        $this->instance(
            ManutencaoService::class,
            Mockery::mock(ManutencaoService::class, function (MockInterface $mock) {
                $mock->shouldReceive('aplicarStatusCarro')
                    ->once()
                    ->with(Mockery::type(Manutencao::class));
            }),
        );

        // Act
        $useCase = app()->make(CreateManutencaoUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Manutencao::class, $result);
        $this->assertSame($expectedResult->id, $result->id);
        $this->assertSame(1, $result->carro_id);
    }

    public function testExecuteShouldCallRepositoryCreateWithCorrectData(): void
    {
        // Arrange
        $data = new CreateManutencaoData(
            carroId: 2,
            tipo: 'corretiva',
            descricao: 'Reparo de freios',
            valor: 500.00,
            kmManutencao: 75000,
            dataManutencao: '2024-02-20',
            status: 'agendada',
            dataProxima: '2024-08-20',
            fornecedor: 'Oficina XYZ',
            observacoes: 'Verificar pastilhas',
        );
        $expectedManutencao = new Manutencao(['id' => 1, 'carro_id' => 2]);

        $repositoryMock = Mockery::mock(ManutencaoRepository::class, function (MockInterface $mock) use ($expectedManutencao) {
            $mock->shouldReceive('create')
                ->once()
                ->with(Mockery::on(function (array $arg) {
                    return $arg['carro_id'] === 2
                        && $arg['tipo'] === 'corretiva'
                        && $arg['descricao'] === 'Reparo de freios'
                        && $arg['valor'] == 500.00
                        && $arg['km_manutencao'] === 75000
                        && $arg['status'] === 'agendada'
                        && $arg['data_proxima'] === '2024-08-20'
                        && $arg['fornecedor'] === 'Oficina XYZ'
                        && $arg['observacoes'] === 'Verificar pastilhas';
                }))
                ->andReturn($expectedManutencao);
        });

        $this->instance(ManutencaoRepository::class, $repositoryMock);
        $this->instance(
            ManutencaoService::class,
            Mockery::mock(ManutencaoService::class, function (MockInterface $mock) {
                $mock->shouldReceive('aplicarStatusCarro')->once();
            }),
        );

        // Act
        $useCase = app()->make(CreateManutencaoUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertSame($expectedManutencao, $result);
    }
}
