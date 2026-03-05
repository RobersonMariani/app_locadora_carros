<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Services;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Models\Locacao;
use Carbon\Carbon;
use InvalidArgumentException;

class LocacaoService
{
    public function __construct(
        private readonly LocacaoRepository $locacaoRepository,
        private readonly CarroRepository $carroRepository,
    ) {}

    public function validarDisponibilidade(int $carroId, string $dataInicio, string $dataFim, ?int $excludeLocacaoId = null): void
    {
        $carro = $this->carroRepository->findById($carroId);

        if ($carro === null) {
            throw new InvalidArgumentException('Carro não encontrado.');
        }

        if (! $carro->disponivel) {
            throw new InvalidArgumentException('Carro indisponível para locação no período informado.');
        }

        if ($this->locacaoRepository->hasConflitoPeriodo($carroId, $dataInicio, $dataFim, $excludeLocacaoId)) {
            throw new InvalidArgumentException('Já existe locação ativa ou reservada para este carro no período informado.');
        }
    }

    public function iniciarLocacao(Locacao $locacao): Locacao
    {
        $statusAtual = $locacao->status;

        if (! $statusAtual->canTransitionTo(LocacaoStatusEnum::ATIVA)) {
            throw new InvalidArgumentException(
                'Não é possível iniciar a locação. Status atual não permite transição para ativa.',
            );
        }

        $locacaoAtualizada = $this->locacaoRepository->updateStatus($locacao->id, LocacaoStatusEnum::ATIVA);
        $this->carroRepository->marcarIndisponivel($locacao->carro_id);

        return $locacaoAtualizada;
    }

    public function finalizarLocacao(Locacao $locacao, array $dadosFinalizacao): Locacao
    {
        $statusAtual = $locacao->status;

        if (! $statusAtual->canTransitionTo(LocacaoStatusEnum::FINALIZADA)) {
            throw new InvalidArgumentException(
                'Não é possível finalizar a locação. Status atual não permite transição para finalizada.',
            );
        }

        $dataFinalRealizado = $dadosFinalizacao['data_final_realizado_periodo'];
        $kmFinal = (int) $dadosFinalizacao['km_final'];

        $dataInicio = $locacao->data_inicio_periodo;
        $dataFinalPrevisto = $locacao->data_final_previsto_periodo;
        $valorDiaria = (float) $locacao->valor_diaria;
        $kmInicial = (int) $locacao->km_inicial;

        $diasRealizados = (int) $dataInicio->diffInDays($dataFinalRealizado) ?: 1;
        $valorTotal = $diasRealizados * $valorDiaria;

        $dataFinalRealizadoCarbon = Carbon::parse($dataFinalRealizado);

        if ($dataFinalRealizadoCarbon->gt($dataFinalPrevisto)) {
            $diasAtraso = (int) $dataFinalPrevisto->diffInDays($dataFinalRealizadoCarbon);
            $multaPercentual = config('locadora.multa_atraso_percentual', 10);
            $valorBaseAtraso = $diasAtraso * $valorDiaria;
            $valorTotal += $valorBaseAtraso * ($multaPercentual / 100);
        }

        $kmLivrePorDia = config('locadora.km_livre_por_dia', 100);
        $kmLivreTotal = $diasRealizados * $kmLivrePorDia;
        $kmRodados = $kmFinal - $kmInicial;

        if ($kmRodados > $kmLivreTotal) {
            $kmExtra = $kmRodados - $kmLivreTotal;
            $custoKmExtra = config('locadora.custo_km_extra', 1.5);
            $valorTotal += $kmExtra * $custoKmExtra;
        }

        $locacaoAtualizada = $this->locacaoRepository->finalizar($locacao->id, [
            'km_final' => $kmFinal,
            'data_final_realizado_periodo' => $dataFinalRealizado,
            'valor_total' => round($valorTotal, 2),
        ]);

        $this->carroRepository->marcarDisponivel($locacao->carro_id);

        return $locacaoAtualizada;
    }

    public function cancelarLocacao(Locacao $locacao): Locacao
    {
        $statusAtual = $locacao->status;

        if (! $statusAtual->canTransitionTo(LocacaoStatusEnum::CANCELADA)) {
            throw new InvalidArgumentException(
                'Não é possível cancelar a locação. Status atual não permite transição para cancelada.',
            );
        }

        $locacaoAtualizada = $this->locacaoRepository->updateStatus($locacao->id, LocacaoStatusEnum::CANCELADA);

        if ($statusAtual === LocacaoStatusEnum::ATIVA) {
            $this->carroRepository->marcarDisponivel($locacao->carro_id);
        }

        return $locacaoAtualizada;
    }
}
