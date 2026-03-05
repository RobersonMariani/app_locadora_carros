<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\UseCases;

use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use Illuminate\Support\Facades\DB;

class DeletePagamentoUseCase
{
    public function __construct(
        private readonly PagamentoRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        DB::transaction(fn () => $this->repository->delete($id));
    }
}
