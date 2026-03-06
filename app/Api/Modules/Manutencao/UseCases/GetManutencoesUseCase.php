<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Manutencao\Data\ManutencaoQueryData;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetManutencoesUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $repository,
    ) {}

    public function execute(ManutencaoQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
