<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\UseCases;

use App\Api\Modules\Locacao\Data\UpdateLocacaoData;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\UseCases\UpdateLocacaoUseCase;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class UpdateLocacaoUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnLocacaoWhenIdExistsAndDataIsValid(): void
    {
        // Arrange
        $existingLocacao = new Locacao([
            'id' => 1,
            'cliente_id' => 1,
            'carro_id' => 1,
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
        ]);
        $updatedLocacao = new Locacao([
            'id' => 1,
            'cliente_id' => 1,
            'carro_id' => 1,
            'valor_diaria' => 200.00,
            'km_inicial' => 1000,
        ]);
        $data = new UpdateLocacaoData(valorDiaria: 200.00);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($existingLocacao, $updatedLocacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($existingLocacao);
                $mock->shouldReceive('update')
                    ->once()
                    ->andReturn($updatedLocacao);
            }),
        );

        // Act
        $useCase = app()->make(UpdateLocacaoUseCase::class);
        $result = $useCase->execute(1, $data);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
        $this->assertEquals($updatedLocacao, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $data = new UpdateLocacaoData(valorDiaria: 200.00);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
                $mock->shouldNotReceive('update');
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(UpdateLocacaoUseCase::class);
        $useCase->execute(999, $data);
    }

    public function testExecuteShouldCallUpdateWithEmptyArrayWhenDataIsEmpty(): void
    {
        // Arrange
        $existingLocacao = new Locacao([
            'id' => 1,
            'cliente_id' => 1,
            'carro_id' => 1,
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
        ]);
        $data = new UpdateLocacaoData;

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($existingLocacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($existingLocacao);
                $mock->shouldReceive('update')
                    ->once()
                    ->with($existingLocacao, [])
                    ->andReturn($existingLocacao);
            }),
        );

        // Act
        $useCase = app()->make(UpdateLocacaoUseCase::class);
        $result = $useCase->execute(1, $data);

        // Assert
        $this->assertInstanceOf(Locacao::class, $result);
    }
}
