<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\UseCases;

use App\Api\Modules\Modelo\Data\CreateModeloData;
use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Api\Modules\Modelo\UseCases\CreateModeloUseCase;
use App\Models\Modelo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modelo')]
class CreateModeloUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnModeloWhenDataIsValid(): void
    {
        // Arrange
        Storage::fake('public');
        $imagem = UploadedFile::fake()->image('modelo.png');
        $data = new CreateModeloData(
            marcaId: 1,
            nome: 'Corolla',
            imagem: $imagem,
            numeroPortas: 4,
            lugares: 5,
            airBag: true,
            abs: true,
        );

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('create')
                    ->once()
                    ->withArgs(function (array $args) use ($data) {
                        return $args['marca_id'] === $data->marcaId
                            && $args['nome'] === $data->nome
                            && isset($args['imagem'])
                            && str_starts_with($args['imagem'], 'imagens/modelos/')
                            && $args['numero_portas'] === $data->numeroPortas
                            && $args['lugares'] === $data->lugares
                            && $args['air_bag'] === $data->airBag
                            && $args['abs'] === $data->abs;
                    })
                    ->andReturnUsing(function (array $args) {
                        return new Modelo([
                            'id' => 1,
                            'marca_id' => $args['marca_id'],
                            'nome' => $args['nome'],
                            'imagem' => $args['imagem'],
                            'numero_portas' => $args['numero_portas'],
                            'lugares' => $args['lugares'],
                            'air_bag' => $args['air_bag'],
                            'abs' => $args['abs'],
                        ]);
                    });
            }),
        );

        // Act
        $useCase = app()->make(CreateModeloUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Modelo::class, $result);
        $this->assertEquals('Corolla', $result->nome);
        $this->assertTrue(Storage::disk('public')->exists($result->imagem));
    }

    public function testExecuteShouldStoreImageInCorrectPathWhenCalled(): void
    {
        // Arrange
        Storage::fake('public');
        $imagem = UploadedFile::fake()->image('modelo.png');
        $data = new CreateModeloData(
            marcaId: 1,
            nome: 'Hilux',
            imagem: $imagem,
            numeroPortas: 4,
            lugares: 5,
            airBag: false,
            abs: true,
        );

        $modelo = new Modelo([
            'id' => 1,
            'marca_id' => 1,
            'nome' => 'Hilux',
            'imagem' => 'imagens/modelos/placeholder.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => false,
            'abs' => true,
        ]);

        $this->instance(
            ModeloRepository::class,
            Mockery::mock(ModeloRepository::class, function (MockInterface $mock) use ($modelo) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturnUsing(function (array $args) use ($modelo) {
                        $modelo->imagem = $args['imagem'];

                        return $modelo;
                    });
            }),
        );

        // Act
        $useCase = app()->make(CreateModeloUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertStringStartsWith('imagens/modelos/', $result->imagem);
        $this->assertTrue(Storage::disk('public')->exists($result->imagem));
    }
}
