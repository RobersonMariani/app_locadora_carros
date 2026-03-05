<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\UseCases;

use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Api\Modules\Modelo\UseCases\DeleteModeloUseCase;
use App\Models\Modelo;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class DeleteModeloUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnTrueAndDeleteImageWhenModeloHasImage(): void
    {
        // Arrange
        Storage::fake('public');
        Storage::disk('public')->put('imagens/modelos/corolla.png', 'fake-content');

        $modelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => 'imagens/modelos/corolla.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($modelo) {
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($modelo)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteModeloUseCase::class);
        $result = $useCase->execute($modelo);

        // Assert
        $this->assertTrue($result);
        $this->assertFalse(Storage::disk('public')->exists('imagens/modelos/corolla.png'));
    }

    public function testExecuteShouldReturnTrueWhenModeloHasNoImage(): void
    {
        // Arrange
        $modelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => null,
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($modelo) {
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($modelo)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteModeloUseCase::class);
        $result = $useCase->execute($modelo);

        // Assert
        $this->assertTrue($result);
    }
}
