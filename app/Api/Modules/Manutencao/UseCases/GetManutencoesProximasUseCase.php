<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use Illuminate\Database\Eloquent\Collection;

class GetManutencoesProximasUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $repository,
    ) {}

    public function execute(int $dias = 7): Collection
    {
        return $this->repository->getProximas($dias);
    }
}
