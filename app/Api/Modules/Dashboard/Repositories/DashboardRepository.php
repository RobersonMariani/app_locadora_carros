<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Repositories;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getResumo(): array
    {
        $totalMarcas = Marca::query()->count();
        $totalModelos = Modelo::query()->count();
        $totalCarros = Carro::query()->count();
        $totalClientes = Cliente::query()->count();
        $carrosDisponiveis = Carro::query()->where('disponivel', true)->count();
        $carrosLocados = Carro::query()->where('disponivel', false)->count();
        $locacoesAtivas = Locacao::query()->where('status', LocacaoStatusEnum::ATIVA)->count();
        $locacoesReservadas = Locacao::query()->where('status', LocacaoStatusEnum::RESERVADA)->count();

        $faturamentoMes = (float) Locacao::query()
            ->where('status', LocacaoStatusEnum::FINALIZADA)
            ->whereMonth('data_final_realizado_periodo', now()->month)
            ->whereYear('data_final_realizado_periodo', now()->year)
            ->sum('valor_total');

        return [
            'total_marcas' => $totalMarcas,
            'total_modelos' => $totalModelos,
            'total_carros' => $totalCarros,
            'total_clientes' => $totalClientes,
            'carros_disponiveis' => $carrosDisponiveis,
            'carros_locados' => $carrosLocados,
            'locacoes_ativas' => $locacoesAtivas,
            'locacoes_reservadas' => $locacoesReservadas,
            'faturamento_mes' => $faturamentoMes,
        ];
    }

    /**
     * @return array<int, array{status: string, label: string, quantidade: int}>
     */
    public function getLocacoesPorStatus(): array
    {
        $contagens = DB::table('locacoes')
            ->selectRaw('status, COUNT(*) as quantidade')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $resultado = [];

        foreach (LocacaoStatusEnum::cases() as $enum) {
            $item = $contagens->get($enum->value);
            $quantidade = $item !== null ? (int) $item->quantidade : 0;
            $resultado[] = [
                'status' => $enum->value,
                'label' => $enum->label(),
                'quantidade' => $quantidade,
            ];
        }

        return $resultado;
    }

    /**
     * @return array<int, array{periodo: string, faturamento: float, quantidade_locacoes: int}>
     */
    public function getFaturamento(string $periodo = 'mensal'): array
    {
        if ($periodo === 'semanal') {
            return $this->getFaturamentoSemanal();
        }

        return $this->getFaturamentoMensal();
    }

    /**
     * @return array<int, array{periodo: string, faturamento: float, quantidade_locacoes: int}>
     */
    private function getFaturamentoMensal(): array
    {
        $inicio = now()->subMonths(11)->startOfMonth();

        $locacoes = Locacao::query()
            ->where('status', LocacaoStatusEnum::FINALIZADA)
            ->whereNotNull('data_final_realizado_periodo')
            ->where('data_final_realizado_periodo', '>=', $inicio)
            ->get(['data_final_realizado_periodo', 'valor_total']);

        $mapa = collect();

        foreach ($locacoes as $locacao) {
            $periodo = $locacao->data_final_realizado_periodo->format('Y-m');
            $valor = (float) $locacao->valor_total;
            $atual = $mapa->get($periodo, ['faturamento' => 0.0, 'quantidade_locacoes' => 0]);
            $atual['faturamento'] += $valor;
            $atual['quantidade_locacoes']++;
            $mapa->put($periodo, $atual);
        }

        return $this->preencherPeriodosMensais($mapa);
    }

    /**
     * @return array<int, array{periodo: string, faturamento: float, quantidade_locacoes: int}>
     */
    private function getFaturamentoSemanal(): array
    {
        $inicio = now()->subWeeks(11)->startOfWeek();

        $locacoes = Locacao::query()
            ->where('status', LocacaoStatusEnum::FINALIZADA)
            ->whereNotNull('data_final_realizado_periodo')
            ->where('data_final_realizado_periodo', '>=', $inicio)
            ->get(['data_final_realizado_periodo', 'valor_total']);

        $mapa = collect();

        foreach ($locacoes as $locacao) {
            $data = $locacao->data_final_realizado_periodo->copy()->startOfWeek();
            $periodo = $data->format('o').'-W'.$data->format('W');
            $valor = (float) $locacao->valor_total;
            $atual = $mapa->get($periodo, ['faturamento' => 0.0, 'quantidade_locacoes' => 0]);
            $atual['faturamento'] += $valor;
            $atual['quantidade_locacoes']++;
            $mapa->put($periodo, $atual);
        }

        return $this->preencherPeriodosSemanais($mapa);
    }

    /**
     * @param Collection<string, array{faturamento: float, quantidade_locacoes: int}> $mapa
     *
     * @return array<int, array{periodo: string, faturamento: float, quantidade_locacoes: int}>
     */
    private function preencherPeriodosMensais(Collection $mapa): array
    {
        $resultado = [];

        for ($i = 11; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $periodo = $data->format('Y-m');
            $item = $mapa->get($periodo);

            $resultado[] = [
                'periodo' => $periodo,
                'faturamento' => $item ? $item['faturamento'] : 0.0,
                'quantidade_locacoes' => $item ? $item['quantidade_locacoes'] : 0,
            ];
        }

        return $resultado;
    }

    /**
     * @param Collection<string, array{faturamento: float, quantidade_locacoes: int}> $mapa
     *
     * @return array<int, array{periodo: string, faturamento: float, quantidade_locacoes: int}>
     */
    private function preencherPeriodosSemanais(Collection $mapa): array
    {
        $resultado = [];

        for ($i = 11; $i >= 0; $i--) {
            $data = now()->subWeeks($i)->startOfWeek();
            $periodo = $data->format('o').'-W'.$data->format('W');
            $item = $mapa->get($periodo);

            $resultado[] = [
                'periodo' => $periodo,
                'faturamento' => $item ? $item['faturamento'] : 0.0,
                'quantidade_locacoes' => $item ? $item['quantidade_locacoes'] : 0,
            ];
        }

        return $resultado;
    }
}
