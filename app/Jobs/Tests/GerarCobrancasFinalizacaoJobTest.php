<?php

declare(strict_types=1);

namespace App\Jobs\Tests;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoStatusEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Jobs\GerarCobrancasFinalizacaoJob;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Pagamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('jobs')]
class GerarCobrancasFinalizacaoJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('locadora.multa_atraso_percentual', 10);
        Config::set('locadora.km_livre_por_dia', 100);
        Config::set('locadora.custo_km_extra', 1.5);
    }

    public function testShouldCreatePagamentoTipoDiariaWhenLocacaoIsFinalizada(): void
    {
        // Arrange
        $locacao = $this->createLocacaoFinalizada(
            dataInicio: '2024-01-01',
            dataFinalPrevisto: '2024-01-05',
            dataFinalRealizado: '2024-01-05',
            valorDiaria: 100.00,
            kmInicial: 10000,
            kmFinal: 10200,
        );

        // Act
        $job = new GerarCobrancasFinalizacaoJob($locacao);
        $job->handle(app()->make(PagamentoRepository::class));

        // Assert
        $pagamentos = Pagamento::query()->where('locacao_id', $locacao->id)->get();
        $this->assertCount(1, $pagamentos);

        $diaria = $pagamentos->first();
        $this->assertEquals(PagamentoTipoEnum::DIARIA, $diaria->tipo);
        $this->assertEquals(400.00, (float) $diaria->valor);
        $this->assertEquals(PagamentoStatusEnum::PENDENTE, $diaria->status);
    }

    public function testShouldCreatePagamentoTipoMultaAtrasoWhenLocacaoHasRetornoAtrasado(): void
    {
        // Arrange
        $locacao = $this->createLocacaoFinalizada(
            dataInicio: '2024-01-01',
            dataFinalPrevisto: '2024-01-05',
            dataFinalRealizado: '2024-01-05',
            valorDiaria: 100.00,
            kmInicial: 10000,
            kmFinal: 10200,
        );

        // Act
        $job = new GerarCobrancasFinalizacaoJob($locacao);
        $job->handle(app()->make(PagamentoRepository::class));

        // Assert - apenas diaria, sem atraso
        $this->assertCount(1, Pagamento::query()->where('locacao_id', $locacao->id)->get());

        // Arrange - locacao com atraso (3 dias)
        $locacaoAtrasada = $this->createLocacaoFinalizada(
            dataInicio: '2024-01-01',
            dataFinalPrevisto: '2024-01-05',
            dataFinalRealizado: '2024-01-08',
            valorDiaria: 100.00,
            kmInicial: 10000,
            kmFinal: 10200,
        );

        // Act
        $jobAtraso = new GerarCobrancasFinalizacaoJob($locacaoAtrasada);
        $jobAtraso->handle(app()->make(PagamentoRepository::class));

        // Assert - diaria + multa_atraso
        $pagamentosAtraso = Pagamento::query()->where('locacao_id', $locacaoAtrasada->id)->get();
        $this->assertCount(2, $pagamentosAtraso);

        $multa = $pagamentosAtraso->firstWhere('tipo', PagamentoTipoEnum::MULTA_ATRASO);
        $this->assertNotNull($multa);
        $this->assertEquals(30.00, (float) $multa->valor);
    }

    public function testShouldCreatePagamentoTipoKmExtraWhenKmExcedente(): void
    {
        // Arrange - 5 dias, 100 km/dia = 500 km livre. 10000 + 700 = 10700 km = 700 rodados. 200 km extra
        $locacao = $this->createLocacaoFinalizada(
            dataInicio: '2024-01-01',
            dataFinalPrevisto: '2024-01-05',
            dataFinalRealizado: '2024-01-05',
            valorDiaria: 100.00,
            kmInicial: 10000,
            kmFinal: 10700,
        );

        // Act
        $job = new GerarCobrancasFinalizacaoJob($locacao);
        $job->handle(app()->make(PagamentoRepository::class));

        // Assert
        $pagamentos = Pagamento::query()->where('locacao_id', $locacao->id)->get();
        $this->assertCount(2, $pagamentos);

        $kmExtra = $pagamentos->firstWhere('tipo', PagamentoTipoEnum::KM_EXTRA);
        $this->assertNotNull($kmExtra);
        $this->assertEquals(450.00, (float) $kmExtra->valor);
    }

    public function testShouldNotCreatePagamentosWhenLocacaoIsNotFinalizada(): void
    {
        // Arrange
        $locacao = $this->createLocacaoWithRelations();
        $locacao->update(['status' => LocacaoStatusEnum::ATIVA]);

        // Act
        $job = new GerarCobrancasFinalizacaoJob($locacao);
        $job->handle(app()->make(PagamentoRepository::class));

        // Assert
        $this->assertCount(0, Pagamento::query()->where('locacao_id', $locacao->id)->get());
    }

    public function testShouldCreateAllThreePagamentosWhenLocacaoHasAtrasoAndKmExtra(): void
    {
        // Arrange - 5 dias realizados (01 a 05), 2 dias atraso (03 a 05), 600 km rodados - 500 livre = 100 extra
        $locacao = $this->createLocacaoFinalizada(
            dataInicio: '2024-01-01',
            dataFinalPrevisto: '2024-01-03',
            dataFinalRealizado: '2024-01-05',
            valorDiaria: 100.00,
            kmInicial: 10000,
            kmFinal: 10600,
        );

        // Act
        $job = new GerarCobrancasFinalizacaoJob($locacao);
        $job->handle(app()->make(PagamentoRepository::class));

        // Assert
        $pagamentos = Pagamento::query()->where('locacao_id', $locacao->id)->get();
        $this->assertCount(3, $pagamentos);

        $diaria = $pagamentos->firstWhere('tipo', PagamentoTipoEnum::DIARIA);
        $this->assertNotNull($diaria);
        $this->assertEquals(400.00, (float) $diaria->valor);

        $multa = $pagamentos->firstWhere('tipo', PagamentoTipoEnum::MULTA_ATRASO);
        $this->assertNotNull($multa);
        $this->assertEquals(20.00, (float) $multa->valor);

        $kmExtra = $pagamentos->firstWhere('tipo', PagamentoTipoEnum::KM_EXTRA);
        $this->assertNotNull($kmExtra);
        $this->assertEquals(300.00, (float) $kmExtra->valor);
    }

    private function createLocacaoFinalizada(
        string $dataInicio,
        string $dataFinalPrevisto,
        string $dataFinalRealizado,
        float $valorDiaria,
        int $kmInicial,
        int $kmFinal,
    ): Locacao {
        $locacao = $this->createLocacaoWithRelations();
        $locacao->update([
            'status' => LocacaoStatusEnum::FINALIZADA,
            'data_inicio_periodo' => $dataInicio,
            'data_final_previsto_periodo' => $dataFinalPrevisto,
            'data_final_realizado_periodo' => $dataFinalRealizado,
            'valor_diaria' => $valorDiaria,
            'km_inicial' => $kmInicial,
            'km_final' => $kmFinal,
        ]);

        return $locacao->refresh();
    }

    private function createLocacaoWithRelations(): Locacao
    {
        $marca = Marca::factory()->create();
        $modelo = Modelo::factory()->create(['marca_id' => $marca->id]);
        $carro = Carro::factory()->create(['modelo_id' => $modelo->id]);
        $cliente = Cliente::factory()->create();

        return Locacao::factory()->create([
            'carro_id' => $carro->id,
            'cliente_id' => $cliente->id,
        ]);
    }
}
