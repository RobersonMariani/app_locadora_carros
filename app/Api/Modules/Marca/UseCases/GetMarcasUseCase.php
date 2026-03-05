<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\UseCases;

use App\Api\Modules\Marca\Data\MarcaQueryData;
use App\Api\Modules\Marca\Repositories\MarcaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetMarcasUseCase
{
    public function __construct(
        private readonly MarcaRepository $repository,
    ) {}

    public function execute(MarcaQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
