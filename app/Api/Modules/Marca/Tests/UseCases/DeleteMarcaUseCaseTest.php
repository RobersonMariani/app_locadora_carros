<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\UseCases;

use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Api\Modules\Marca\UseCases\DeleteMarcaUseCase;
use App\Models\Marca;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class DeleteMarcaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnTrueAndDeleteImageWhenMarcaHasImage(): void
    {
        // Arrange
        Storage::fake('public');
        Storage::disk('public')->put('imagens/marcas/toyota.png', 'fake-content');

        $marca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => 'imagens/marcas/toyota.png']);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($marca) {
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($marca)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteMarcaUseCase::class);
        $result = $useCase->execute($marca);

        // Assert
        $this->assertTrue($result);
        $this->assertFalse(Storage::disk('public')->exists('imagens/marcas/toyota.png'));
    }

    public function testExecuteShouldReturnTrueWhenMarcaHasNoImage(): void
    {
        // Arrange
        $marca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => null]);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($marca) {
                $mock->shouldReceive('delete')
                    ->once()
                    ->with($marca)
                    ->andReturn(true);
            }),
        );

        // Act
        $useCase = app()->make(DeleteMarcaUseCase::class);
        $result = $useCase->execute($marca);

        // Assert
        $this->assertTrue($result);
    }
}
