<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\UseCases;

use App\Api\Modules\Multa\Data\MultaQueryData;
use App\Api\Modules\Multa\Repositories\MultaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetMultasUseCase
{
    public function __construct(
        private readonly MultaRepository $repository,
    ) {}

    public function execute(MultaQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
