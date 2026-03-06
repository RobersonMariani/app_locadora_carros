<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\Services;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Api\Modules\Vistoria\Repositories\VistoriaRepository;
use App\Api\Modules\Vistoria\Services\VistoriaService;
use App\Models\Locacao;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('vistoria')]
class VistoriaServiceTest extends TestCase
{
    public function testValidarCriacaoShouldPassWhenLocacaoExistsAndRetiradaIsFirst(): void
    {
        // Arrange
        $locacao = new Locacao(['id' => 1]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($locacao);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::RETIRADA)
                    ->andReturn(false);
            }),
        );

        $service = app()->make(VistoriaService::class);

        // Act & Assert
        $service->validarCriacao(1, VistoriaTipoEnum::RETIRADA);
    }

    public function testValidarCriacaoShouldThrowWhenLocacaoDoesNotExist(): void
    {
        // Arrange
        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(99999)
                    ->andReturn(null);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('hasVistoriaByTipo')->never();
            }),
        );

        $service = app()->make(VistoriaService::class);

        $this->expectException(ValidationException::class);

        try {
            $service->validarCriacao(99999, VistoriaTipoEnum::RETIRADA);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('locacao_id', $e->errors());

            throw $e;
        }
    }

    public function testValidarCriacaoShouldThrowWhenRetiradaIsDuplicate(): void
    {
        // Arrange
        $locacao = new Locacao(['id' => 1]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($locacao);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::RETIRADA)
                    ->andReturn(true);
            }),
        );

        $service = app()->make(VistoriaService::class);

        $this->expectException(ValidationException::class);

        try {
            $service->validarCriacao(1, VistoriaTipoEnum::RETIRADA);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('tipo', $e->errors());
            $this->assertStringContainsString('retirada', $e->errors()['tipo'][0]);

            throw $e;
        }
    }

    public function testValidarCriacaoShouldThrowWhenDevolucaoWithoutRetirada(): void
    {
        // Arrange
        $locacao = new Locacao(['id' => 1]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($locacao);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::RETIRADA)
                    ->andReturn(false);
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->with(1, VistoriaTipoEnum::DEVOLUCAO)
                    ->never();
            }),
        );

        $service = app()->make(VistoriaService::class);

        $this->expectException(ValidationException::class);

        try {
            $service->validarCriacao(1, VistoriaTipoEnum::DEVOLUCAO);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('tipo', $e->errors());
            $this->assertStringContainsString('retirada', $e->errors()['tipo'][0]);

            throw $e;
        }
    }

    public function testValidarCriacaoShouldThrowWhenDevolucaoIsDuplicate(): void
    {
        // Arrange
        $locacao = new Locacao(['id' => 1]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($locacao);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::RETIRADA)
                    ->andReturn(true);
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::DEVOLUCAO)
                    ->andReturn(true);
            }),
        );

        $service = app()->make(VistoriaService::class);

        $this->expectException(ValidationException::class);

        try {
            $service->validarCriacao(1, VistoriaTipoEnum::DEVOLUCAO);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('tipo', $e->errors());
            $this->assertStringContainsString('devolução', $e->errors()['tipo'][0]);

            throw $e;
        }
    }

    public function testValidarCriacaoShouldPassWhenDevolucaoAfterRetirada(): void
    {
        // Arrange
        $locacao = new Locacao(['id' => 1]);

        $this->instance(
            LocacaoRepository::class,
            Mockery::mock(LocacaoRepository::class, function (MockInterface $mock) use ($locacao) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($locacao);
            }),
        );

        $this->instance(
            VistoriaRepository::class,
            Mockery::mock(VistoriaRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::RETIRADA)
                    ->andReturn(true);
                $mock->shouldReceive('hasVistoriaByTipo')
                    ->once()
                    ->with(1, VistoriaTipoEnum::DEVOLUCAO)
                    ->andReturn(false);
            }),
        );

        $service = app()->make(VistoriaService::class);

        // Act & Assert
        $service->validarCriacao(1, VistoriaTipoEnum::DEVOLUCAO);
    }
}
