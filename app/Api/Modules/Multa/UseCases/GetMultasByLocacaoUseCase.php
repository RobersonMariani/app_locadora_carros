<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\UseCases;

use App\Api\Modules\Multa\Data\MultaQueryData;
use App\Api\Modules\Multa\Repositories\MultaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GetMultasByLocacaoUseCase
{
    public function __construct(
        private readonly MultaRepository $repository,
    ) {}

    public function execute(int $locacaoId, ?MultaQueryData $query = null): LengthAwarePaginator|Collection
    {
        return $this->repository->getByLocacao($locacaoId, $query);
    }
}
