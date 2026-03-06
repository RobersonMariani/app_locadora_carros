<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Services;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Models\Manutencao;

class ManutencaoService
{
    public function __construct(
        private readonly CarroRepository $carroRepository,
    ) {}

    public function aplicarStatusCarro(Manutencao $manutencao, ?ManutencaoStatusEnum $statusAnterior = null): void
    {
        $statusAtual = $manutencao->status;
        $carroId = $manutencao->carro_id;

        if ($statusAtual === ManutencaoStatusEnum::EM_ANDAMENTO) {
            $this->carroRepository->marcarIndisponivel($carroId);

            return;
        }

        if ($statusAtual === ManutencaoStatusEnum::CONCLUIDA || $statusAtual === ManutencaoStatusEnum::CANCELADA) {
            $this->carroRepository->marcarDisponivel($carroId);
        }
    }
}
