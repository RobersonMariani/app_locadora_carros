<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\UseCases;

use App\Api\Modules\Modelo\Data\UpdateModeloData;
use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Api\Modules\Modelo\UseCases\UpdateModeloUseCase;
use App\Models\Modelo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class UpdateModeloUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnUpdatedModeloWhenOnlyNomeProvided(): void
    {
        // Arrange
        $modelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => 'imagens/modelos/old.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);
        $data = new UpdateModeloData(nome: 'Corolla Atualizado', imagem: null);

        $updatedModelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla Atualizado',
            'imagem' => 'imagens/modelos/old.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($updatedModelo) {
                $mock->shouldReceive('update')
                    ->once()
                    ->withArgs(function (Modelo $m, array $args) {
                        return $args['nome'] === 'Corolla Atualizado' && ! isset($args['imagem']);
                    })
                    ->andReturn($updatedModelo);
            }),
        );

        // Act
        $useCase = app()->make(UpdateModeloUseCase::class);
        $result = $useCase->execute($modelo, $data);

        // Assert
        $this->assertInstanceOf(Modelo::class, $result);
        $this->assertEquals('Corolla Atualizado', $result->nome);
    }

    public function testExecuteShouldReplaceImageWhenNewImagemProvided(): void
    {
        // Arrange
        Storage::fake('public');
        Storage::disk('public')->put('imagens/modelos/old.png', 'fake-content');

        $modelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => 'imagens/modelos/old.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);
        $novaImagem = UploadedFile::fake()->image('modelo.png');
        $data = new UpdateModeloData(nome: null, imagem: $novaImagem);

        $updatedModelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => 'imagens/modelos/new.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($updatedModelo) {
                $mock->shouldReceive('update')
                    ->once()
                    ->withArgs(function (Modelo $m, array $args) {
                        return isset($args['imagem'])
                            && str_starts_with($args['imagem'], 'imagens/modelos/');
                    })
                    ->andReturn($updatedModelo);
            }),
        );

        // Act
        $useCase = app()->make(UpdateModeloUseCase::class);
        $result = $useCase->execute($modelo, $data);

        // Assert
        $this->assertInstanceOf(Modelo::class, $result);
        $this->assertFalse(Storage::disk('public')->exists('imagens/modelos/old.png'));
    }

    public function testExecuteShouldNotDeleteImageWhenModeloHasNoImage(): void
    {
        // Arrange
        Storage::fake('public');
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
        $novaImagem = UploadedFile::fake()->image('modelo.png');
        $data = new UpdateModeloData(nome: null, imagem: $novaImagem);

        $updatedModelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Corolla',
            'imagem' => 'imagens/modelos/new.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($updatedModelo) {
                $mock->shouldReceive('update')
                    ->once()
                    ->andReturn($updatedModelo);
            }),
        );

        // Act
        $useCase = app()->make(UpdateModeloUseCase::class);
        $result = $useCase->execute($modelo, $data);

        // Assert
        $this->assertInstanceOf(Modelo::class, $result);
    }
}
