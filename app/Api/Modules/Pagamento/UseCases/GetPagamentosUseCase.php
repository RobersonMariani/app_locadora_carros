<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\UseCases;

use App\Api\Modules\Pagamento\Data\PagamentoQueryData;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetPagamentosUseCase
{
    public function __construct(
        private readonly PagamentoRepository $repository,
    ) {}

    public function execute(PagamentoQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAll($query);
    }
}
