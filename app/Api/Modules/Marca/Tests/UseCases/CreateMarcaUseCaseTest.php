<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\UseCases;

use App\Api\Modules\Marca\Data\CreateMarcaData;
use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Api\Modules\Marca\UseCases\CreateMarcaUseCase;
use App\Models\Marca;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('marca')]
class CreateMarcaUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnMarcaWhenDataIsValid(): void
    {
        // Arrange
        Storage::fake('public');
        $imagem = UploadedFile::fake()->image('marca.png');
        $data = new CreateMarcaData(nome: 'Toyota', imagem: $imagem);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('create')
                    ->once()
                    ->withArgs(function (array $args) use ($data) {
                        return $args['nome'] === $data->nome
                            && isset($args['imagem'])
                            && str_starts_with($args['imagem'], 'imagens/marcas/');
                    })
                    ->andReturnUsing(function (array $args) {
                        return new Marca([
                            'id' => 1,
                            'nome' => $args['nome'],
                            'imagem' => $args['imagem'],
                        ]);
                    });
            }),
        );

        // Act
        $useCase = app()->make(CreateMarcaUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Marca::class, $result);
        $this->assertEquals('Toyota', $result->nome);
        $this->assertTrue(Storage::disk('public')->exists($result->imagem));
    }

    public function testExecuteShouldStoreImageInCorrectPathWhenCalled(): void
    {
        // Arrange
        Storage::fake('public');
        $imagem = UploadedFile::fake()->image('marca.png');
        $data = new CreateMarcaData(nome: 'Honda', imagem: $imagem);

        $savedPath = 'imagens/marcas/test.png';
        $marca = new Marca(['id' => 1, 'nome' => 'Honda', 'imagem' => $savedPath]);

        $this->instance(
            MarcaRepository::class,
            Mockery::mock(MarcaRepository::class, function (MockInterface $mock) use ($marca) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturnUsing(function (array $args) use ($marca) {
                        $marca->imagem = $args['imagem'];

                        return $marca;
                    });
            }),
        );

        // Act
        $useCase = app()->make(CreateMarcaUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertStringStartsWith('imagens/marcas/', $result->imagem);
        $this->assertTrue(Storage::disk('public')->exists($result->imagem));
    }
}
