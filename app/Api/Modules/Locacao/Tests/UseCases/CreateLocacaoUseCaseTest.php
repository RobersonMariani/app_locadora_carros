<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Locacao\Data\CreateLocacaoData;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Api\Modules\Locacao\UseCases\CreateLocacaoUseCase;
use App\Models\Cliente;
use App\Models\Locacao;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class CreateLocacaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLocacaoWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreateLocacaoData(
            clienteId: 1,
            carroId: 1,
            dataInicioPeriodo: '2024-01-01',
            dataFinalPrevistoPeriodo: '2024-01-10',
            dataFinalRealizadoPeriodo: null,
            valorDiaria: 150.50,
            kmInicial: 1000,
            kmFinal: null,
        );
        $clienteAtivo = new Cliente(['id' => 1, 'bloqueado' => false]);
        $expectedResult = new Locacao([
            'id' => 1,
            'cliente_id' => 1,
            'carro_id' => 1,
            'status' => LocacaoStatusEnum::RESERVADA->value,
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
        ]);

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($clienteAtivo) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($clienteAtivo);
            }),
        );

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        $this->instance(
            LocacaoService::class,
            Mockery::mock(LocacaoService::class, function (MockInterface $mock) {
                $mock->shouldReceive('validarDisponibilidade')
                    ->once()
                    ->with(1, '2024-01-01', '2024-01-10');
            }),
        );

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class),
        );

        // Act
        $useCase = app()->make(CreateLocacaoUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertSame($expectedResult->id, $result->id);
    }

    public function testExecuteShouldThrowInvalidArgumentExceptionWhenClienteIsBloqueado(): void
    {
        // Arrange
        $data = new CreateLocacaoData(
            clienteId: 1,
            carroId: 1,
            dataInicioPeriodo: '2024-01-01',
            dataFinalPrevistoPeriodo: '2024-01-10',
            dataFinalRealizadoPeriodo: null,
            valorDiaria: 150.50,
            kmInicial: 1000,
            kmFinal: null,
        );
        $clienteBloqueado = new Cliente(['id' => 1, 'bloqueado' => true]);

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($clienteBloqueado) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($clienteBloqueado);
            }),
        );

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class),
        );

        $this->instance(
            LocacaoService::class,
            Mockery::mock(LocacaoService::class),
        );

        $this->instance(
            CarroRepository::class,
            Mockery::mock(CarroRepository::class),
        );

        $this->expectException(ValidationException::class);

        // Act
        $useCase = app()->make(CreateLocacaoUseCase::class);
        $useCase->execute($data);
    }
}
