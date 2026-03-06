<?php

declare(strict_types=1);

namespace App\Jobs\Tests;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use App\Api\Modules\Alerta\Repositories\AlertaRepository;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Jobs\VerificarLocacoesAtrasadasJob;
use App\Models\Alerta;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('jobs')]
class VerificarLocacoesAtrasadasJobTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldMarcarAtrasadaAndCreateAlertaWhenLocacaoIsActiveAndOverdue(): void
    {
        // Arrange
        $locacao = $this->createLocacaoAtivaAtrasada();

        // Act
        $job = new VerificarLocacoesAtrasadasJob;
        $job->handle(
            app()->make(LocacaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert
        $locacao->refresh();
        $this->assertTrue($locacao->atrasada);

        $alerta = Alerta::query()
            ->where('tipo', AlertaTipoEnum::LOCACAO_ATRASADA->value)
            ->where('referencia_type', 'App\Models\Locacao')
            ->where('referencia_id', $locacao->id)
            ->first();

        $this->assertNotNull($alerta);
        $this->assertEquals('Locação atrasada', $alerta->titulo);
        $this->assertStringContainsString("Locação #{$locacao->id}", $alerta->descricao);
        $this->assertFalse($alerta->lido);
    }

    public function testShouldNotProcessLocacaoWhenAlreadyMarkedAsAtrasada(): void
    {
        // Arrange
        $locacao = $this->createLocacaoAtivaAtrasada();
        $locacao->update(['atrasada' => true]);

        $initialCount = Alerta::query()->count();

        // Act
        $job = new VerificarLocacoesAtrasadasJob;
        $job->handle(
            app()->make(LocacaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert - getLocacoesAtivasAtrasadas excludes atrasada=true, so no new alerta
        $this->assertEquals($initialCount, Alerta::query()->count());
    }

    public function testShouldProcessMultipleLocacoesAtrasadas(): void
    {
        // Arrange
        $locacao1 = $this->createLocacaoAtivaAtrasada();
        $locacao2 = $this->createLocacaoAtivaAtrasada();

        // Act
        $job = new VerificarLocacoesAtrasadasJob;
        $job->handle(
            app()->make(LocacaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert
        $this->assertTrue($locacao1->fresh()->atrasada);
        $this->assertTrue($locacao2->fresh()->atrasada);

        $alertas = Alerta::query()
            ->where('tipo', AlertaTipoEnum::LOCACAO_ATRASADA->value)
            ->get();

        $this->assertCount(2, $alertas);
    }

    private function createLocacaoAtivaAtrasada(): Locacao
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);
        $cliente = Cliente::factory()->create();

        return Locacao::factory()->create([
            'carro_id' => $carro->id,
            'cliente_id' => $cliente->id,
            'status' => LocacaoStatusEnum::ATIVA,
            'data_inicio_periodo' => now()->subDays(10),
            'data_final_previsto_periodo' => now()->subDays(2),
            'data_final_realizado_periodo' => null,
            'atrasada' => false,
        ]);
    }
}
