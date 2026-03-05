<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\UseCases;

use App\Api\Modules\Marca\Data\UpdateMarcaData;
use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Api\Modules\Marca\UseCases\UpdateMarcaUseCase;
use App\Models\Marca;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class UpdateMarcaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnUpdatedMarcaWhenOnlyNomeProvided(): void
    {
        // Arrange
        $marca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => 'imagens/marcas/old.png']);
        $data = new UpdateMarcaData(nome: 'Toyota Atualizado', imagem: null);

        $updatedMarca = new Marca(['id' => 1, 'nome' => 'Toyota Atualizado', 'imagem' => 'imagens/marcas/old.png']);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($updatedMarca) {
                $mock->shouldReceive('update')
                    ->once()
                    ->withArgs(function (Marca $m, array $args) {
                        return $args['nome'] === 'Toyota Atualizado' && ! isset($args['imagem']);
                    })
                    ->andReturn($updatedMarca);
            }),
        );

        // Act
        $useCase = app()->make(UpdateMarcaUseCase::class);
        $result = $useCase->execute($marca, $data);

        // Assert
        $this->assertInstanceOf(Marca::class, $result);
        $this->assertEquals('Toyota Atualizado', $result->nome);
    }

    public function testExecuteShouldReplaceImageWhenNewImagemProvided(): void
    {
        // Arrange
        Storage::fake('public');
        Storage::disk('public')->put('imagens/marcas/old.png', 'fake-content');

        $marca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => 'imagens/marcas/old.png']);
        $novaImagem = UploadedFile::fake()->image('marca.png');
        $data = new UpdateMarcaData(nome: null, imagem: $novaImagem);

        $updatedMarca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => 'imagens/marcas/new.png']);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($updatedMarca) {
                $mock->shouldReceive('update')
                    ->once()
                    ->withArgs(function (Marca $m, array $args) {
                        return isset($args['imagem'])
                            && str_starts_with($args['imagem'], 'imagens/marcas/');
                    })
                    ->andReturn($updatedMarca);
            }),
        );

        // Act
        $useCase = app()->make(UpdateMarcaUseCase::class);
        $result = $useCase->execute($marca, $data);

        // Assert
        $this->assertInstanceOf(Marca::class, $result);
        $this->assertFalse(Storage::disk('public')->exists('imagens/marcas/old.png'));
    }

    public function testExecuteShouldNotDeleteImageWhenMarcaHasNoImage(): void
    {
        // Arrange
        Storage::fake('public');
        $marca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => null]);
        $novaImagem = UploadedFile::fake()->image('marca.png');
        $data = new UpdateMarcaData(nome: null, imagem: $novaImagem);

        $updatedMarca = new Marca(['id' => 1, 'nome' => 'Toyota', 'imagem' => 'imagens/marcas/new.png']);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($updatedMarca) {
                $mock->shouldReceive('update')
                    ->once()
                    ->andReturn($updatedMarca);
            }),
        );

        // Act
        $useCase = app()->make(UpdateMarcaUseCase::class);
        $result = $useCase->execute($marca, $data);

        // Assert
        $this->assertInstanceOf(Marca::class, $result);
    }
}
