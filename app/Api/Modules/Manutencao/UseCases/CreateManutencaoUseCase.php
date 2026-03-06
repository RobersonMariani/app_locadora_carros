<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Manutencao\Data\CreateManutencaoData;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use App\Api\Modules\Manutencao\Services\ManutencaoService;
use App\Models\Manutencao;
use Illuminate\Support\Facades\DB;

class CreateManutencaoUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $repository,
        private readonly ManutencaoService $manutencaoService,
    ) {}

    public function execute(CreateManutencaoData $data): Manutencao
    {
        return DB::transaction(function () use ($data) {
            $manutencao = $this->repository->create($data->toArrayModel());
            $this->manutencaoService->aplicarStatusCarro($manutencao);

            return $manutencao;
        });
    }
}
