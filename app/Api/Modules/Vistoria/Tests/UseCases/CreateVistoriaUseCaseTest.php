<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\UseCases;

use App\Api\Modules\Vistoria\Data\CreateVistoriaData;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Api\Modules\Vistoria\Repositories\VistoriaRepository;
use App\Api\Modules\Vistoria\Services\VistoriaService;
use App\Api\Modules\Vistoria\UseCases\CreateVistoriaUseCase;
use App\Models\User;
use App\Models\Vistoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class CreateVistoriaUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function testExecuteShouldReturnVistoriaWhenDataIsValid(): void
    {
        // Arrange
        $user = User::factory()->create();
        Auth::guard('api')->login($user);

        $data = new CreateVistoriaData(
            locacaoId: 1,
            tipo: 'retirada',
            combustivelNivel: 'metade',
            kmRegistrado: 50000,
            observacoes: null,
            dataVistoria: '2024-01-15',
        );

        $expectedResult = new Vistoria([
            'id' => 1,
            'locacao_id' => 1,
            'tipo' => 'retirada',
            'combustivel_nivel' => 'metade',
            'km_registrado' => 50000,
            'realizado_por' => $user->id,
        ]);

        $this->instance(
            VistoriaService::class,
            Mockery::mock(VistoriaService::class, function (MockInterface $mock) {
                $mock->shouldReceive('validarCriacao')
                    ->once()
                    ->with(1, VistoriaTipoEnum::RETIRADA);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(CreateVistoriaUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Vistoria::class, $result);
        $this->assertSame($expectedResult->id, $result->id);
    }

    public function testExecuteShouldThrowValidationExceptionWhenServiceValidatesFailure(): void
    {
        // Arrange
        $user = User::factory()->create();
        Auth::guard('api')->login($user);

        $data = new CreateVistoriaData(
            locacaoId: 99999,
            tipo: 'retirada',
            combustivelNivel: 'metade',
            kmRegistrado: 50000,
            observacoes: null,
            dataVistoria: '2024-01-15',
        );

        $this->instance(
            VistoriaService::class,
            Mockery::mock(VistoriaService::class, function (MockInterface $mock) {
                $mock->shouldReceive('validarCriacao')
                    ->once()
                    ->andThrow(ValidationException::withMessages([
                        'locacao_id' => ['Locação não encontrada.'],
                    ]));
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('create')->never();
            }),
        );

        $this->expectException(ValidationException::class);

        // Act
        $useCase = app()->make(CreateVistoriaUseCase::class);
        $useCase->execute($data);
    }
}
