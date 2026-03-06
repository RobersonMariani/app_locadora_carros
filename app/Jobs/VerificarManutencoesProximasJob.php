<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use App\Api\Modules\Alerta\Repositories\AlertaRepository;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerificarManutencoesProximasJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(ManutencaoRepository $manutencaoRepository, AlertaRepository $alertaRepository): void
    {
        $manutencoes = $manutencaoRepository->getProximas(7);

        foreach ($manutencoes as $manutencao) {
            if ($alertaRepository->existeAlertaHoje(
                AlertaTipoEnum::MANUTENCAO_PROXIMA->value,
                'App\Models\Manutencao',
                $manutencao->id,
            )) {
                continue;
            }

            $dataProxima = $manutencao->data_proxima->format('d/m/Y');
            $titulo = $manutencao->data_proxima->isPast()
                ? 'Manutenção vencida'
                : 'Manutenção próxima';

            $alertaRepository->create([
                'tipo' => AlertaTipoEnum::MANUTENCAO_PROXIMA->value,
                'titulo' => $titulo,
                'descricao' => "Manutenção #{$manutencao->id} ({$manutencao->descricao}) - data próxima: {$dataProxima}.",
                'referencia_type' => 'App\Models\Manutencao',
                'referencia_id' => $manutencao->id,
                'lido' => false,
                'data_alerta' => now(),
            ]);
        }
    }
}
