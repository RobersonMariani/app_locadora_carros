<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\UseCases;

use App\Api\Modules\Modelo\Data\ModeloQueryData;
use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetModelosUseCase
{
    public function __construct(
        private readonly ModeloRepository $repository,
    ) {}

    public function execute(ModeloQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
