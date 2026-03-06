<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoStatusEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Models\Locacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GerarCobrancasFinalizacaoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly Locacao $locacao,
    ) {}

    public function handle(PagamentoRepository $pagamentoRepository): void
    {
        $locacao = $this->locacao->refresh();

        if (! $locacao->isFinalizada()) {
            return;
        }

        $dataInicio = $locacao->data_inicio_periodo;
        $dataFinalRealizado = $locacao->data_final_realizado_periodo;
        $dataFinalPrevisto = $locacao->data_final_previsto_periodo;
        $valorDiaria = (float) $locacao->valor_diaria;
        $kmInicial = (int) $locacao->km_inicial;
        $kmFinal = (int) $locacao->km_final;

        $diasRealizados = (int) $dataInicio->diffInDays($dataFinalRealizado) ?: 1;
        $dataPagamento = $dataFinalRealizado->format('Y-m-d');

        $pagamentoRepository->create([
            'locacao_id' => $locacao->id,
            'valor' => $diasRealizados * $valorDiaria,
            'tipo' => PagamentoTipoEnum::DIARIA->value,
            'status' => PagamentoStatusEnum::PENDENTE->value,
            'metodo_pagamento' => MetodoPagamentoEnum::PIX->value,
            'data_pagamento' => $dataPagamento,
        ]);

        if ($dataFinalRealizado->gt($dataFinalPrevisto)) {
            $diasAtraso = (int) $dataFinalPrevisto->diffInDays($dataFinalRealizado);
            $multaPercentual = config('locadora.multa_atraso_percentual', 10);
            $valorBaseAtraso = $diasAtraso * $valorDiaria;
            $valorMulta = round($valorBaseAtraso * ($multaPercentual / 100), 2);

            $pagamentoRepository->create([
                'locacao_id' => $locacao->id,
                'valor' => $valorMulta,
                'tipo' => PagamentoTipoEnum::MULTA_ATRASO->value,
                'status' => PagamentoStatusEnum::PENDENTE->value,
                'metodo_pagamento' => MetodoPagamentoEnum::PIX->value,
                'data_pagamento' => $dataPagamento,
            ]);
        }

        $kmLivrePorDia = config('locadora.km_livre_por_dia', 100);
        $kmLivreTotal = $diasRealizados * $kmLivrePorDia;
        $kmRodados = $kmFinal - $kmInicial;

        if ($kmRodados > $kmLivreTotal) {
            $kmExtra = $kmRodados - $kmLivreTotal;
            $custoKmExtra = config('locadora.custo_km_extra', 1.5);
            $valorKmExtra = round($kmExtra * $custoKmExtra, 2);

            $pagamentoRepository->create([
                'locacao_id' => $locacao->id,
                'valor' => $valorKmExtra,
                'tipo' => PagamentoTipoEnum::KM_EXTRA->value,
                'status' => PagamentoStatusEnum::PENDENTE->value,
                'metodo_pagamento' => MetodoPagamentoEnum::PIX->value,
                'data_pagamento' => $dataPagamento,
            ]);
        }
    }
}
