<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\UseCases;

use App\Api\Modules\Multa\Data\UpdateMultaData;
use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Api\Modules\Multa\UseCases\UpdateMultaUseCase;
use App\Models\Multa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class UpdateMultaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnUpdatedMultaWhenIdExists(): void
    {
        // Arrange
        $existingMulta = new Multa([
            'id' => 1,
            'locacao_id' => 1,
            'valor' => 150.50,
            'descricao' => 'Excesso de velocidade',
            'status' => 'pendente',
        ]);
        $updatedMulta = new Multa([
            'id' => 1,
            'locacao_id' => 1,
            'valor' => 200.00,
            'descricao' => 'Descrição atualizada',
            'status' => 'paga',
        ]);
        $data = new UpdateMultaData(
            valor: 200.00,
            descricao: 'Descrição atualizada',
            status: 'paga',
        );

        $this->instance(
            MultaRepository::class,
            Mockery::mock(MultaRepository::class, function (MockInterface $mock) use ($existingMulta, $updatedMulta) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($existingMulta);
                $mock->shouldReceive('update')
                    ->once()
                    ->andReturn($updatedMulta);
            }),
        );

        // Act
        $useCase = app()->make(UpdateMultaUseCase::class);
        $result = $useCase->execute(1, $data);

        // Assert
        $this->assertInstanceOf(Multa::class, $result);
        $this->assertEquals(200.00, $result->valor);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenIdDoesNotExist(): void
    {
        // Arrange
        $data = new UpdateMultaData(descricao: 'Nova descrição');

        $this->instance(
            MultaRepository::class,
            Mockery::mock(MultaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(UpdateMultaUseCase::class);
        $useCase->execute(999, $data);
    }
}
