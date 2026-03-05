<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\UseCases;

use App\Api\Modules\Pagamento\Data\CreatePagamentoData;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Models\Pagamento;
use Illuminate\Support\Facades\DB;

class CreatePagamentoUseCase
{
    public function __construct(
        private readonly PagamentoRepository $repository,
    ) {}

    public function execute(CreatePagamentoData $data): Pagamento
    {
        return DB::transaction(fn () => $this->repository->create($data->toArrayModel()));
    }
}
