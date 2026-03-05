<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\UseCases;

use App\Api\Modules\Cliente\Data\ClienteQueryData;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetClientesUseCase
{
    public function __construct(
        private readonly ClienteRepository $repository,
    ) {}

    public function execute(ClienteQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
