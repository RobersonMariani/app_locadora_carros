<?php

declare(strict_types=1);

namespace App\Jobs\Tests;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use App\Api\Modules\Alerta\Repositories\AlertaRepository;
use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use App\Jobs\VerificarManutencoesProximasJob;
use App\Models\Alerta;
use App\Models\Carro;
use App\Models\Manutencao;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('jobs')]
class VerificarManutencoesProximasJobTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldCreateAlertaWhenManutencaoIsProxima(): void
    {
        // Arrange
        $manutencao = $this->createManutencaoProxima(dias: 3);

        // Act
        $job = new VerificarManutencoesProximasJob;
        $job->handle(
            app()->make(ManutencaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert
        $alerta = Alerta::query()
            ->where('tipo', AlertaTipoEnum::MANUTENCAO_PROXIMA->value)
            ->where('referencia_type', 'App\Models\Manutencao')
            ->where('referencia_id', $manutencao->id)
            ->first();

        $this->assertNotNull($alerta);
        $this->assertEquals('Manutenção próxima', $alerta->titulo);
        $this->assertStringContainsString("Manutenção #{$manutencao->id}", $alerta->descricao);
        $this->assertFalse($alerta->lido);
    }

    public function testShouldCreateAlertaWithTituloManutencaoVencidaWhenDataProximaIsPast(): void
    {
        // Arrange
        $manutencao = $this->createManutencaoProxima(dias: -2);

        // Act
        $job = new VerificarManutencoesProximasJob;
        $job->handle(
            app()->make(ManutencaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert
        $alerta = Alerta::query()
            ->where('referencia_id', $manutencao->id)
            ->first();

        $this->assertNotNull($alerta);
        $this->assertEquals('Manutenção vencida', $alerta->titulo);
    }

    public function testShouldNotCreateDuplicateAlertaWhenAlertaAlreadyExistsToday(): void
    {
        // Arrange
        $manutencao = $this->createManutencaoProxima(dias: 3);

        Alerta::factory()->create([
            'tipo' => AlertaTipoEnum::MANUTENCAO_PROXIMA->value,
            'referencia_type' => 'App\Models\Manutencao',
            'referencia_id' => $manutencao->id,
            'data_alerta' => now(),
        ]);

        $initialCount = Alerta::query()->count();

        // Act
        $job = new VerificarManutencoesProximasJob;
        $job->handle(
            app()->make(ManutencaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert
        $this->assertEquals($initialCount, Alerta::query()->count());
    }

    public function testShouldCreateAlertasForMultipleManutencoesProximas(): void
    {
        // Arrange
        $manutencao1 = $this->createManutencaoProxima(dias: 1);
        $manutencao2 = $this->createManutencaoProxima(dias: 5);

        // Act
        $job = new VerificarManutencoesProximasJob;
        $job->handle(
            app()->make(ManutencaoRepository::class),
            app()->make(AlertaRepository::class),
        );

        // Assert
        $alertas = Alerta::query()
            ->where('tipo', AlertaTipoEnum::MANUTENCAO_PROXIMA->value)
            ->where('referencia_type', 'App\Models\Manutencao')
            ->get();

        $this->assertCount(2, $alertas);
    }

    private function createManutencaoProxima(int $dias): Manutencao
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);

        return Manutencao::factory()->create([
            'carro_id' => $carro->id,
            'data_proxima' => now()->addDays($dias),
            'data_manutencao' => now()->subDays(30),
            'status' => ManutencaoStatusEnum::AGENDADA->value,
            'descricao' => 'Troca de óleo',
        ]);
    }
}
