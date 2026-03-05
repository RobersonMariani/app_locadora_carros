<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\UseCases;

use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Models\Pagamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetPagamentoUseCase
{
    public function __construct(
        private readonly PagamentoRepository $repository,
    ) {}

    public function execute(int $id): Pagamento
    {
        $pagamento = $this->repository->findById($id);

        if ($pagamento === null) {
            throw new ModelNotFoundException;
        }

        return $pagamento;
    }
}
