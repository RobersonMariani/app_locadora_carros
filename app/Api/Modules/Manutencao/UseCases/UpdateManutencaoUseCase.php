<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Manutencao\Data\UpdateManutencaoData;
use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use App\Api\Modules\Manutencao\Services\ManutencaoService;
use App\Models\Manutencao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateManutencaoUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $repository,
        private readonly ManutencaoService $manutencaoService,
    ) {}

    public function execute(int $id, UpdateManutencaoData $data): Manutencao
    {
        $manutencao = $this->repository->findById($id);

        if ($manutencao === null) {
            throw new ModelNotFoundException;
        }

        $statusAnterior = $manutencao->status instanceof ManutencaoStatusEnum ? $manutencao->status : null;
        $updateData = $data->toArrayModel();

        if (empty($updateData)) {
            return $manutencao;
        }

        return DB::transaction(function () use ($manutencao, $updateData, $statusAnterior) {
            $manutencaoAtualizada = $this->repository->update($manutencao, $updateData);
            $this->manutencaoService->aplicarStatusCarro($manutencaoAtualizada, $statusAnterior);

            return $manutencaoAtualizada;
        });
    }
}
