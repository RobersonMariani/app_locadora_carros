<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use App\Api\Modules\Alerta\Repositories\AlertaRepository;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerificarLocacoesAtrasadasJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(LocacaoRepository $locacaoRepository, AlertaRepository $alertaRepository): void
    {
        $locacoesAtrasadas = $locacaoRepository->getLocacoesAtivasAtrasadas();

        foreach ($locacoesAtrasadas as $locacao) {
            $locacaoRepository->marcarAtrasada($locacao->id);

            $alertaRepository->create([
                'tipo' => AlertaTipoEnum::LOCACAO_ATRASADA->value,
                'titulo' => 'Locação atrasada',
                'descricao' => "Locação #{$locacao->id} está atrasada. Data prevista: {$locacao->data_final_previsto_periodo->format('d/m/Y')}.",
                'referencia_type' => 'App\Models\Locacao',
                'referencia_id' => $locacao->id,
                'lido' => false,
                'data_alerta' => now(),
            ]);
        }
    }
}
